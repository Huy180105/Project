package com.huy.smartqueue.data.repository

import android.content.Context
import com.huy.smartqueue.data.api.RetrofitClient
import com.huy.smartqueue.data.model.LoginRequest
import com.huy.smartqueue.data.model.RegisterRequest
import com.huy.smartqueue.data.model.User
import com.huy.smartqueue.datastore.TokenDataStore

class AuthRepository(
    private val context: Context
) {
    private val api = RetrofitClient.create(context.applicationContext)
    private val tokenStore = TokenDataStore(context.applicationContext)

    suspend fun login(email: String, password: String): Result<String> {
        return try {
            val response = api.login(LoginRequest(email, password))

            if (response.isSuccessful && response.body()?.success == true) {
                val token = response.body()?.data?.token

                if (!token.isNullOrBlank()) {
                    tokenStore.saveToken(token)
                    Result.success(response.body()?.message ?: "Đăng nhập thành công")
                } else {
                    Result.failure(Exception("Không nhận được token"))
                }
            } else {
                Result.failure(Exception(response.body()?.message ?: "Đăng nhập thất bại"))
            }
        } catch (exception: Exception) {
            Result.failure(exception)
        }
    }

    suspend fun register(request: RegisterRequest): Result<String> {
        return try {
            val response = api.register(request)

            if (response.isSuccessful && response.body()?.success == true) {
                val token = response.body()?.data?.token

                if (!token.isNullOrBlank()) {
                    tokenStore.saveToken(token)
                    Result.success(response.body()?.message ?: "Đăng ký thành công")
                } else {
                    Result.failure(Exception("Không nhận được token"))
                }
            } else {
                Result.failure(Exception(response.body()?.message ?: "Đăng ký thất bại"))
            }
        } catch (exception: Exception) {
            Result.failure(exception)
        }
    }

    suspend fun profile(): Result<User> {
        return try {
            val response = api.profile()
            val user = response.body()?.data

            if (response.isSuccessful && response.body()?.success == true && user != null) {
                Result.success(user)
            } else {
                Result.failure(Exception(response.body()?.message ?: "Không tải được hồ sơ bệnh nhân"))
            }
        } catch (exception: Exception) {
            Result.failure(exception)
        }
    }
}
