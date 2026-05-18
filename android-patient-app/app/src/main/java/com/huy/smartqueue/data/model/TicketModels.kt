package com.huy.smartqueue.data.model

import com.google.gson.annotations.SerializedName

data class CreateTicketRequest(
    @SerializedName("department_id")
    val departmentId: Int,
    @SerializedName("patient_phone")
    val patientPhone: String?,
    @SerializedName("priority_reason")
    val priorityReason: String
)

data class QueueTicketDepartment(
    val id: Int,
    val name: String,
    @SerializedName("room_number")
    val roomNumber: String
)

data class QueueTicket(
    val id: Int,
    @SerializedName("queue_number")
    val queueNumber: String,
    @SerializedName("patient_name")
    val patientName: String,
    @SerializedName("patient_phone")
    val patientPhone: String?,
    val status: String,
    @SerializedName("status_label")
    val statusLabel: String,
    val channel: String,
    @SerializedName("estimated_wait_time")
    val estimatedWaitTime: Int?,
    @SerializedName("priority_level")
    val priorityLevel: Int,
    @SerializedName("priority_reason")
    val priorityReason: String,
    @SerializedName("priority_reason_label")
    val priorityReasonLabel: String,
    val department: QueueTicketDepartment,
    @SerializedName("queue_position")
    val queuePosition: Int,
    @SerializedName("remaining_before_me")
    val remainingBeforeMe: Int,
    @SerializedName("current_calling_number")
    val currentCallingNumber: String?,
    @SerializedName("department_room")
    val departmentRoom: String,
    @SerializedName("updated_at")
    val updatedAt: String?
)

data class TicketQrPayload(
    @SerializedName("ticket_id")
    val ticketId: Int,
    @SerializedName("queue_number")
    val queueNumber: String,
    @SerializedName("department_id")
    val departmentId: Int,
    @SerializedName("patient_name")
    val patientName: String,
    @SerializedName("issued_at")
    val issuedAt: String?,
    @SerializedName("qr_payload")
    val qrPayload: String
)
