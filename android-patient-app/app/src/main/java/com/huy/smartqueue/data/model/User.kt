package com.huy.smartqueue.data.model

data class User(
    val id: Int,
    val name: String,
    val email: String,
    val role: String,
    @com.google.gson.annotations.SerializedName("patient_profile")
    val patientProfile: PatientProfile?
)
