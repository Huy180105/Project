package com.huy.smartqueue.viewmodel

import android.app.Application
import androidx.lifecycle.AndroidViewModel
import androidx.lifecycle.viewModelScope
import com.huy.smartqueue.data.model.Department
import com.huy.smartqueue.data.model.QueueTicket
import com.huy.smartqueue.data.model.TicketQrPayload
import com.huy.smartqueue.data.repository.QueueRepository
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.launch

class QueueViewModel(application: Application) : AndroidViewModel(application) {
    private val repository = QueueRepository(application)

    private val _departmentsState = MutableStateFlow<List<Department>>(emptyList())
    val departmentsState: StateFlow<List<Department>> = _departmentsState.asStateFlow()

    private val _loadingState = MutableStateFlow(false)
    val loadingState: StateFlow<Boolean> = _loadingState.asStateFlow()

    private val _errorState = MutableStateFlow<String?>(null)
    val errorState: StateFlow<String?> = _errorState.asStateFlow()

    private val _ticketState = MutableStateFlow<QueueTicket?>(null)
    val ticketState: StateFlow<QueueTicket?> = _ticketState.asStateFlow()

    private val _createTicketLoading = MutableStateFlow(false)
    val createTicketLoading: StateFlow<Boolean> = _createTicketLoading.asStateFlow()

    private val _createTicketError = MutableStateFlow<String?>(null)
    val createTicketError: StateFlow<String?> = _createTicketError.asStateFlow()

    private val _myTicketState = MutableStateFlow<QueueTicket?>(null)
    val myTicketState: StateFlow<QueueTicket?> = _myTicketState.asStateFlow()

    private val _myTicketLoading = MutableStateFlow(false)
    val myTicketLoading: StateFlow<Boolean> = _myTicketLoading.asStateFlow()

    private val _myTicketError = MutableStateFlow<String?>(null)
    val myTicketError: StateFlow<String?> = _myTicketError.asStateFlow()

    private val _ticketQrState = MutableStateFlow<TicketQrPayload?>(null)
    val ticketQrState: StateFlow<TicketQrPayload?> = _ticketQrState.asStateFlow()

    fun loadDepartments() {
        viewModelScope.launch {
            _loadingState.value = true
            _errorState.value = null

            repository.getDepartments()
                .onSuccess { departments ->
                    _departmentsState.value = departments
                }
                .onFailure { exception ->
                    _errorState.value = exception.message ?: "Không tải được danh sách khoa"
                }

            _loadingState.value = false
        }
    }

    fun createTicket(departmentId: Int, patientPhone: String?, priorityReason: String) {
        viewModelScope.launch {
            _createTicketLoading.value = true
            _createTicketError.value = null

            repository.createTicket(departmentId, patientPhone, priorityReason)
                .onSuccess { ticket ->
                    _ticketState.value = ticket
                }
                .onFailure { exception ->
                    _createTicketError.value = exception.message ?: "Không tạo được phiếu khám"
                }

            _createTicketLoading.value = false
        }
    }

    fun loadMyTicket() {
        viewModelScope.launch {
            _myTicketLoading.value = true
            _myTicketError.value = null

            repository.getMyTicket()
                .onSuccess { ticket ->
                    _myTicketState.value = ticket
                }
                .onFailure { exception ->
                    _myTicketError.value = exception.message ?: "Không tải được phiếu khám hiện tại"
                }

            _myTicketLoading.value = false
        }
    }

    fun refreshTicketStatus(ticketId: Int) {
        viewModelScope.launch {
            _myTicketLoading.value = true
            _myTicketError.value = null

            repository.getQueueStatus(ticketId)
                .onSuccess { ticket ->
                    _myTicketState.value = ticket
                }
                .onFailure { exception ->
                    _myTicketError.value = exception.message ?: "Không cập nhật được trạng thái phiếu"
                }

            _myTicketLoading.value = false
        }
    }

    fun loadTicketQr(ticketId: Int) {
        viewModelScope.launch {
            repository.getTicketQr(ticketId)
                .onSuccess { payload ->
                    _ticketQrState.value = payload
                }
                .onFailure { exception ->
                    _myTicketError.value = exception.message ?: "Không tải được mã QR"
                }
        }
    }
}
