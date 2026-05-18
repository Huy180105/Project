package com.huy.smartqueue.viewmodel

import android.app.Application
import androidx.lifecycle.AndroidViewModel
import androidx.lifecycle.viewModelScope
import com.huy.smartqueue.data.model.RegisterRequest
import com.huy.smartqueue.data.model.User
import com.huy.smartqueue.data.repository.AuthRepository
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.launch

class AuthViewModel(application: Application) : AndroidViewModel(application) {
    private val repository = AuthRepository(application)

    private val _loginState = MutableStateFlow<String?>(null)
    val loginState: StateFlow<String?> = _loginState.asStateFlow()

    private val _errorState = MutableStateFlow<String?>(null)
    val errorState: StateFlow<String?> = _errorState.asStateFlow()

    private val _registerState = MutableStateFlow<String?>(null)
    val registerState: StateFlow<String?> = _registerState.asStateFlow()

    private val _registerLoading = MutableStateFlow(false)
    val registerLoading: StateFlow<Boolean> = _registerLoading.asStateFlow()

    private val _registerError = MutableStateFlow<String?>(null)
    val registerError: StateFlow<String?> = _registerError.asStateFlow()

    private val _profileState = MutableStateFlow<User?>(null)
    val profileState: StateFlow<User?> = _profileState.asStateFlow()

    fun login(email: String, password: String) {
        viewModelScope.launch {
            val result = repository.login(email, password)

            result.onSuccess {
                _errorState.value = null
                _loginState.value = it
            }

            result.onFailure {
                _loginState.value = null
                _errorState.value = it.message
            }
        }
    }

    fun register(request: RegisterRequest) {
        viewModelScope.launch {
            _registerLoading.value = true
            _registerError.value = null

            repository.register(request)
                .onSuccess {
                    _registerState.value = it
                }
                .onFailure {
                    _registerError.value = it.message
                }

            _registerLoading.value = false
        }
    }

    fun loadProfile() {
        viewModelScope.launch {
            repository.profile()
                .onSuccess {
                    _profileState.value = it
                }
        }
    }
}
