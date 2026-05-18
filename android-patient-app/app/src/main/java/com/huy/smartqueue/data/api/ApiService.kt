package com.huy.smartqueue.data.api

import com.huy.smartqueue.data.model.ApiResponse
import com.huy.smartqueue.data.model.AuthData
import com.huy.smartqueue.data.model.Department
import com.huy.smartqueue.data.model.LoginRequest
import com.huy.smartqueue.data.model.CreateTicketRequest
import com.huy.smartqueue.data.model.QueueTicket
import com.huy.smartqueue.data.model.TicketQrPayload
import com.huy.smartqueue.data.model.RegisterRequest
import com.huy.smartqueue.data.model.User
import retrofit2.Response
import retrofit2.http.Body
import retrofit2.http.GET
import retrofit2.http.Path
import retrofit2.http.PUT
import retrofit2.http.POST

interface ApiService {
    @POST("login")
    suspend fun login(
        @Body request: LoginRequest
    ): Response<ApiResponse<AuthData>>

    @POST("register")
    suspend fun register(
        @Body request: RegisterRequest
    ): Response<ApiResponse<AuthData>>

    @GET("profile")
    suspend fun profile(): Response<ApiResponse<User>>

    @PUT("profile")
    suspend fun updateProfile(
        @Body request: Map<String, String?>
    ): Response<ApiResponse<User>>

    @GET("departments")
    suspend fun departments(): Response<ApiResponse<List<Department>>>

    @POST("tickets")
    suspend fun createTicket(
        @Body request: CreateTicketRequest
    ): Response<ApiResponse<QueueTicket>>

    @GET("my-ticket")
    suspend fun myTicket(): Response<ApiResponse<QueueTicket>>

    @GET("queue-status/{ticket}")
    suspend fun queueStatus(
        @Path("ticket") ticketId: Int
    ): Response<ApiResponse<QueueTicket>>

    @GET("tickets/{ticket}/qr")
    suspend fun ticketQr(
        @Path("ticket") ticketId: Int
    ): Response<ApiResponse<TicketQrPayload>>
}
