package com.huy.smartqueue.data.model

import com.google.gson.annotations.SerializedName

data class Department(
    val id: Int,
    val name: String,
    @SerializedName("room_number")
    val roomNumber: String,
    @SerializedName("current_number")
    val currentNumber: String?,
    @SerializedName("average_time_per_patient")
    val averageTimePerPatient: Int
)
