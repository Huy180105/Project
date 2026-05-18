package com.huy.smartqueue.data.model

data class LoginRequest(
    val email: String,
    val password: String
)

data class RegisterRequest(
    val name: String,
    val email: String,
    val password: String,
    val password_confirmation: String,
    val phone: String,
    val dob: String?,
    val gender: String?,
    val insurance_number: String?,
    val citizen_id: String?,
    val address: String?,
    val emergency_contact_name: String?,
    val emergency_contact_phone: String?,
    val medical_history: String?,
    val allergies: String?
)

data class AuthData(
    val user: User,
    val token: String
)
