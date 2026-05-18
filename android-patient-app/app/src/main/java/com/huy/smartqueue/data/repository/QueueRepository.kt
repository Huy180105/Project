package com.huy.smartqueue.data.repository

import android.content.Context
import com.huy.smartqueue.data.api.RetrofitClient
import com.huy.smartqueue.data.model.Department
import com.huy.smartqueue.data.model.CreateTicketRequest
import com.huy.smartqueue.data.model.QueueTicket
import com.huy.smartqueue.data.model.TicketQrPayload

class QueueRepository(
    context: Context
) {
    private val api = RetrofitClient.create(context.applicationContext)

    suspend fun getDepartments(): Result<List<Department>> {
        return try {
            val response = api.departments()

            if (response.isSuccessful && response.body()?.success == true) {
                Result.success(response.body()?.data.orEmpty())
            } else {
                Result.failure(Exception(response.body()?.message ?: "Không tải được danh sách khoa"))
            }
        } catch (exception: Exception) {
            Result.failure(exception)
        }
    }

    suspend fun createTicket(
        departmentId: Int,
        patientPhone: String?,
        priorityReason: String
    ): Result<QueueTicket> {
        return try {
            val response = api.createTicket(CreateTicketRequest(departmentId, patientPhone, priorityReason))

            if (response.isSuccessful && response.body()?.success == true && response.body()?.data != null) {
                Result.success(response.body()!!.data!!)
            } else {
                Result.failure(Exception(response.body()?.message ?: "Không tạo được phiếu khám"))
            }
        } catch (exception: Exception) {
            Result.failure(exception)
        }
    }

    suspend fun getMyTicket(): Result<QueueTicket?> {
        return try {
            val response = api.myTicket()

            if (response.isSuccessful && response.body()?.success == true) {
                Result.success(response.body()?.data)
            } else {
                Result.failure(Exception(response.body()?.message ?: "Không tải được phiếu khám hiện tại"))
            }
        } catch (exception: Exception) {
            Result.failure(exception)
        }
    }

    suspend fun getQueueStatus(ticketId: Int): Result<QueueTicket> {
        return try {
            val response = api.queueStatus(ticketId)

            if (response.isSuccessful && response.body()?.success == true && response.body()?.data != null) {
                Result.success(response.body()!!.data!!)
            } else {
                Result.failure(Exception(response.body()?.message ?: "Không cập nhật được trạng thái phiếu"))
            }
        } catch (exception: Exception) {
            Result.failure(exception)
        }
    }

    suspend fun getTicketQr(ticketId: Int): Result<TicketQrPayload> {
        return try {
            val response = api.ticketQr(ticketId)

            if (response.isSuccessful && response.body()?.success == true && response.body()?.data != null) {
                Result.success(response.body()!!.data!!)
            } else {
                Result.failure(Exception(response.body()?.message ?: "Không tải được mã QR"))
            }
        } catch (exception: Exception) {
            Result.failure(exception)
        }
    }
}
