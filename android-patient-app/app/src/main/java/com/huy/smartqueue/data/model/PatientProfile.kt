package com.huy.smartqueue.data.model

import com.google.gson.annotations.SerializedName

data class PatientProfile(
    val phone: String,
    val dob: String?,
    val gender: String?,
    @SerializedName("insurance_number")
    val insuranceNumber: String?,
    @SerializedName("citizen_id")
    val citizenId: String?,
    val address: String?,
    @SerializedName("emergency_contact_name")
    val emergencyContactName: String?,
    @SerializedName("emergency_contact_phone")
    val emergencyContactPhone: String?,
    @SerializedName("medical_history")
    val medicalHistory: String?,
    val allergies: String?
)
