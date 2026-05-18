package com.huy.smartqueue.ui.ticket

import android.content.Context
import android.content.Intent
import android.Manifest
import android.content.pm.PackageManager
import android.graphics.Bitmap
import android.graphics.Color
import android.os.Bundle
import android.view.View
import androidx.activity.viewModels
import androidx.activity.result.contract.ActivityResultContracts
import androidx.appcompat.app.AppCompatActivity
import androidx.core.content.ContextCompat
import androidx.lifecycle.Lifecycle
import androidx.lifecycle.lifecycleScope
import androidx.lifecycle.repeatOnLifecycle
import com.google.zxing.BarcodeFormat
import com.google.zxing.MultiFormatWriter
import com.huy.smartqueue.data.model.QueueTicket
import com.huy.smartqueue.databinding.ActivityMyTicketBinding
import com.huy.smartqueue.ui.departments.DepartmentListActivity
import com.huy.smartqueue.viewmodel.QueueViewModel
import kotlinx.coroutines.Job
import kotlinx.coroutines.delay
import kotlinx.coroutines.isActive
import kotlinx.coroutines.launch

class MyTicketActivity : AppCompatActivity() {
    private lateinit var binding: ActivityMyTicketBinding
    private val viewModel: QueueViewModel by viewModels()
    private lateinit var notificationHelper: NotificationHelper
    private var ticketId: Int = -1
    private var pollingJob: Job? = null
    private var terminalTicket = false
    private var nearTurnNotificationShown = false
    private var callingNotificationShown = false
    private val requestNotificationPermission = registerForActivityResult(
        ActivityResultContracts.RequestPermission()
    ) { }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityMyTicketBinding.inflate(layoutInflater)
        setContentView(binding.root)
        notificationHelper = NotificationHelper(this)
        requestNotificationPermissionIfNeeded()

        ticketId = intent.getIntExtra(EXTRA_TICKET_ID, -1)
        renderTicketFromIntent()

        binding.refreshStatusButton.setOnClickListener {
            refreshStatus()
        }

        binding.backToDepartmentsButton.setOnClickListener {
            startActivity(Intent(this, DepartmentListActivity::class.java))
            finish()
        }

        observeState()

        if (ticketId > 0) {
            viewModel.loadTicketQr(ticketId)
        }
    }

    override fun onStart() {
        super.onStart()
        startPolling()
    }

    override fun onStop() {
        stopPolling()
        super.onStop()
    }

    override fun onDestroy() {
        stopPolling()
        super.onDestroy()
    }

    private fun refreshStatus() {
        if (ticketId > 0 && !terminalTicket) {
            viewModel.refreshTicketStatus(ticketId)
        }
    }

    private fun startPolling() {
        if (ticketId <= 0 || terminalTicket || pollingJob?.isActive == true) {
            return
        }

        pollingJob = lifecycleScope.launch {
            while (isActive && !terminalTicket) {
                delay(POLLING_INTERVAL_MS)
                refreshStatus()
            }
        }
    }

    private fun stopPolling() {
        pollingJob?.cancel()
        pollingJob = null
    }

    private fun renderTicketFromIntent() {
        renderFields(
            queueNumber = intent.getStringExtra(EXTRA_QUEUE_NUMBER),
            patientName = intent.getStringExtra(EXTRA_PATIENT_NAME),
            patientPhone = intent.getStringExtra(EXTRA_PATIENT_PHONE),
            departmentName = intent.getStringExtra(EXTRA_DEPARTMENT_NAME),
            roomNumber = intent.getStringExtra(EXTRA_ROOM_NUMBER),
            status = intent.getStringExtra(EXTRA_STATUS),
            statusLabel = intent.getStringExtra(EXTRA_STATUS_LABEL),
            estimatedWait = intent.getIntExtra(EXTRA_ESTIMATED_WAIT, 0),
            priorityReason = intent.getStringExtra(EXTRA_PRIORITY_REASON_LABEL),
            queuePosition = intent.getIntExtra(EXTRA_QUEUE_POSITION, 0),
            remainingBeforeMe = intent.getIntExtra(EXTRA_REMAINING_BEFORE_ME, 0),
            currentCallingNumber = intent.getStringExtra(EXTRA_CURRENT_CALLING_NUMBER),
            updatedAt = intent.getStringExtra(EXTRA_UPDATED_AT),
        )
    }

    private fun renderTicket(ticket: QueueTicket) {
        ticketId = ticket.id
        renderFields(
            queueNumber = ticket.queueNumber,
            patientName = ticket.patientName,
            patientPhone = ticket.patientPhone,
            departmentName = ticket.department.name,
            roomNumber = ticket.departmentRoom,
            status = ticket.status,
            statusLabel = ticket.statusLabel,
            estimatedWait = ticket.estimatedWaitTime ?: 0,
            priorityReason = ticket.priorityReasonLabel,
            queuePosition = ticket.queuePosition,
            remainingBeforeMe = ticket.remainingBeforeMe,
            currentCallingNumber = ticket.currentCallingNumber,
            updatedAt = ticket.updatedAt,
        )
    }

    private fun renderFields(
        queueNumber: String?,
        patientName: String?,
        patientPhone: String?,
        departmentName: String?,
        roomNumber: String?,
        status: String?,
        statusLabel: String?,
        estimatedWait: Int,
        priorityReason: String?,
        queuePosition: Int,
        remainingBeforeMe: Int,
        currentCallingNumber: String?,
        updatedAt: String?,
    ) {
        binding.queueNumber.text = queueNumber
        binding.patientName.text = "Bệnh nhân: ${patientName.orEmpty()}"
        binding.patientPhone.text = "Số điện thoại: ${patientPhone ?: "Chưa có số điện thoại"}"
        binding.departmentName.text = "Khoa: ${departmentName.orEmpty()}"
        binding.roomNumber.text = "Phòng: ${roomNumber.orEmpty()}"
        binding.statusLabel.text = "Trạng thái: ${statusLabel.orEmpty()}"
        binding.estimatedWait.text = "Thời gian chờ ước tính: $estimatedWait phút"
        binding.priorityReason.text = "Lý do ưu tiên: ${priorityReason.orEmpty()}"
        binding.queuePosition.text = "Vị trí trong hàng đợi: $queuePosition"
        binding.remainingBeforeMe.text = "Còn trước bạn: $remainingBeforeMe lượt"
        binding.currentCallingNumber.text = "Đang gọi: ${currentCallingNumber ?: "Đang chờ lượt gọi"}"
        binding.updatedAt.text = "Cập nhật gần nhất: ${updatedAt ?: "Chưa có cập nhật"}"

        terminalTicket = status == STATUS_COMPLETED || status == STATUS_CANCELLED
        updateNearTurnState(
            queueNumber = queueNumber.orEmpty(),
            departmentName = roomNumber.orEmpty(),
            status = status,
            remainingBeforeMe = remainingBeforeMe,
        )
        binding.finalStateMessage.visibility = if (terminalTicket) View.VISIBLE else View.GONE
        binding.finalStateMessage.text = when (status) {
            STATUS_COMPLETED -> "Phiếu khám đã hoàn thành."
            STATUS_CANCELLED -> "Phiếu khám đã bị hủy."
            else -> ""
        }

        if (terminalTicket) {
            stopPolling()
        }
    }

    private fun updateNearTurnState(
        queueNumber: String,
        departmentName: String,
        status: String?,
        remainingBeforeMe: Int,
    ) {
        val shouldWarnNearTurn = remainingBeforeMe <= 5 && (status == STATUS_READY || status == STATUS_CALLING)
        binding.nearTurnCard.visibility = if (shouldWarnNearTurn) View.VISIBLE else View.GONE

        if (shouldWarnNearTurn && !nearTurnNotificationShown) {
            notificationHelper.showNearTurnNotification(queueNumber, remainingBeforeMe, departmentName)
            nearTurnNotificationShown = true
        }

        if (status == STATUS_CALLING && !callingNotificationShown) {
            notificationHelper.showCallingNotification(queueNumber, departmentName)
            callingNotificationShown = true
        }
    }

    private fun requestNotificationPermissionIfNeeded() {
        if (
            android.os.Build.VERSION.SDK_INT >= android.os.Build.VERSION_CODES.TIRAMISU &&
            ContextCompat.checkSelfPermission(this, Manifest.permission.POST_NOTIFICATIONS) != PackageManager.PERMISSION_GRANTED
        ) {
            requestNotificationPermission.launch(Manifest.permission.POST_NOTIFICATIONS)
        }
    }

    private fun observeState() {
        lifecycleScope.launch {
            repeatOnLifecycle(Lifecycle.State.STARTED) {
                launch {
                    viewModel.myTicketLoading.collect { isLoading ->
                        binding.refreshStatusButton.isEnabled = !isLoading && !terminalTicket
                        binding.refreshProgress.visibility = if (isLoading) View.VISIBLE else View.GONE
                    }
                }
                launch {
                    viewModel.myTicketError.collect { error ->
                        binding.errorText.text = error
                        binding.errorText.visibility = if (error.isNullOrBlank()) View.GONE else View.VISIBLE
                    }
                }
                launch {
                    viewModel.myTicketState.collect { ticket ->
                        if (ticket != null) {
                            renderTicket(ticket)
                        }
                    }
                }
                launch {
                    viewModel.ticketQrState.collect { payload ->
                        if (payload != null) {
                            binding.qrCodeImage.setImageBitmap(createQrBitmap(payload.qrPayload))
                            binding.qrHint.visibility = View.VISIBLE
                        }
                    }
                }
            }
        }
    }

    private fun createQrBitmap(payload: String): Bitmap {
        val matrix = MultiFormatWriter().encode(payload, BarcodeFormat.QR_CODE, QR_SIZE, QR_SIZE)
        val bitmap = Bitmap.createBitmap(QR_SIZE, QR_SIZE, Bitmap.Config.RGB_565)

        for (x in 0 until QR_SIZE) {
            for (y in 0 until QR_SIZE) {
                bitmap.setPixel(x, y, if (matrix[x, y]) Color.BLACK else Color.WHITE)
            }
        }

        return bitmap
    }

    companion object {
        private const val POLLING_INTERVAL_MS = 10_000L
        private const val QR_SIZE = 480
        private const val STATUS_COMPLETED = "completed"
        private const val STATUS_CANCELLED = "cancelled"
        private const val STATUS_READY = "ready"
        private const val STATUS_CALLING = "calling"
        private const val EXTRA_TICKET_ID = "ticket_id"
        private const val EXTRA_QUEUE_NUMBER = "queue_number"
        private const val EXTRA_PATIENT_NAME = "patient_name"
        private const val EXTRA_PATIENT_PHONE = "patient_phone"
        private const val EXTRA_DEPARTMENT_NAME = "department_name"
        private const val EXTRA_ROOM_NUMBER = "room_number"
        private const val EXTRA_STATUS = "status"
        private const val EXTRA_STATUS_LABEL = "status_label"
        private const val EXTRA_ESTIMATED_WAIT = "estimated_wait"
        private const val EXTRA_PRIORITY_REASON_LABEL = "priority_reason_label"
        private const val EXTRA_QUEUE_POSITION = "queue_position"
        private const val EXTRA_REMAINING_BEFORE_ME = "remaining_before_me"
        private const val EXTRA_CURRENT_CALLING_NUMBER = "current_calling_number"
        private const val EXTRA_UPDATED_AT = "updated_at"

        fun intent(context: Context, ticket: QueueTicket): Intent {
            return Intent(context, MyTicketActivity::class.java).apply {
                putExtra(EXTRA_TICKET_ID, ticket.id)
                putExtra(EXTRA_QUEUE_NUMBER, ticket.queueNumber)
                putExtra(EXTRA_PATIENT_NAME, ticket.patientName)
                putExtra(EXTRA_PATIENT_PHONE, ticket.patientPhone)
                putExtra(EXTRA_DEPARTMENT_NAME, ticket.department.name)
                putExtra(EXTRA_ROOM_NUMBER, ticket.departmentRoom)
                putExtra(EXTRA_STATUS, ticket.status)
                putExtra(EXTRA_STATUS_LABEL, ticket.statusLabel)
                putExtra(EXTRA_ESTIMATED_WAIT, ticket.estimatedWaitTime ?: 0)
                putExtra(EXTRA_PRIORITY_REASON_LABEL, ticket.priorityReasonLabel)
                putExtra(EXTRA_QUEUE_POSITION, ticket.queuePosition)
                putExtra(EXTRA_REMAINING_BEFORE_ME, ticket.remainingBeforeMe)
                putExtra(EXTRA_CURRENT_CALLING_NUMBER, ticket.currentCallingNumber)
                putExtra(EXTRA_UPDATED_AT, ticket.updatedAt)
            }
        }
    }
}
