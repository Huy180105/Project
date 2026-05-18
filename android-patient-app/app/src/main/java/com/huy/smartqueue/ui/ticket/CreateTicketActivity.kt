package com.huy.smartqueue.ui.ticket

import android.content.Context
import android.content.Intent
import android.os.Bundle
import android.view.View
import android.widget.ArrayAdapter
import androidx.activity.viewModels
import androidx.appcompat.app.AppCompatActivity
import androidx.lifecycle.Lifecycle
import androidx.lifecycle.lifecycleScope
import androidx.lifecycle.repeatOnLifecycle
import com.huy.smartqueue.databinding.ActivityCreateTicketBinding
import com.huy.smartqueue.viewmodel.AuthViewModel
import com.huy.smartqueue.viewmodel.QueueViewModel
import kotlinx.coroutines.launch

class CreateTicketActivity : AppCompatActivity() {
    private lateinit var binding: ActivityCreateTicketBinding
    private val authViewModel: AuthViewModel by viewModels()
    private val viewModel: QueueViewModel by viewModels()
    private val priorityReasons = listOf(
        "none",
        "emergency",
        "child_under_6",
        "pregnant",
        "severe_disability",
        "elderly_75",
        "revolutionary_contributor",
    )

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityCreateTicketBinding.inflate(layoutInflater)
        setContentView(binding.root)

        val departmentId = intent.getIntExtra(EXTRA_DEPARTMENT_ID, -1)
        binding.departmentName.text = intent.getStringExtra(EXTRA_DEPARTMENT_NAME)
        binding.roomNumber.text = intent.getStringExtra(EXTRA_ROOM_NUMBER)
        binding.priorityReasonInput.setAdapter(ArrayAdapter(this, android.R.layout.simple_list_item_1, priorityReasons))
        binding.priorityReasonInput.setText(priorityReasons.first(), false)

        binding.createTicketButton.setOnClickListener {
            viewModel.createTicket(
                departmentId = departmentId,
                patientPhone = binding.phoneInput.text?.toString()?.trim().takeUnless { it.isNullOrBlank() },
                priorityReason = binding.priorityReasonInput.text?.toString().orEmpty(),
            )
        }

        observeState()
        authViewModel.loadProfile()
    }

    private fun observeState() {
        lifecycleScope.launch {
            repeatOnLifecycle(Lifecycle.State.STARTED) {
                launch {
                    authViewModel.profileState.collect { user ->
                        if (binding.phoneInput.text.isNullOrBlank()) {
                            binding.phoneInput.setText(user?.patientProfile?.phone.orEmpty())
                        }
                    }
                }
                launch {
                    viewModel.createTicketLoading.collect { loading ->
                        binding.createTicketProgress.visibility = if (loading) View.VISIBLE else View.GONE
                        binding.createTicketButton.isEnabled = !loading
                    }
                }
                launch {
                    viewModel.createTicketError.collect { error ->
                        binding.errorText.text = error
                        binding.errorText.visibility = if (error.isNullOrBlank()) View.GONE else View.VISIBLE
                    }
                }
                launch {
                    viewModel.ticketState.collect { ticket ->
                        if (ticket != null) {
                            startActivity(MyTicketActivity.intent(this@CreateTicketActivity, ticket))
                            finish()
                        }
                    }
                }
            }
        }
    }

    companion object {
        private const val EXTRA_DEPARTMENT_ID = "department_id"
        private const val EXTRA_DEPARTMENT_NAME = "department_name"
        private const val EXTRA_ROOM_NUMBER = "room_number"

        fun intent(context: Context, departmentId: Int, departmentName: String, roomNumber: String): Intent {
            return Intent(context, CreateTicketActivity::class.java).apply {
                putExtra(EXTRA_DEPARTMENT_ID, departmentId)
                putExtra(EXTRA_DEPARTMENT_NAME, departmentName)
                putExtra(EXTRA_ROOM_NUMBER, roomNumber)
            }
        }
    }
}
