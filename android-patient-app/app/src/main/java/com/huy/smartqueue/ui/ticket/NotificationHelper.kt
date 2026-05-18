package com.huy.smartqueue.ui.ticket

import android.Manifest
import android.app.NotificationChannel
import android.app.NotificationManager
import android.content.Context
import android.content.pm.PackageManager
import android.os.Build
import androidx.core.app.NotificationCompat
import androidx.core.app.NotificationManagerCompat
import androidx.core.content.ContextCompat
import com.huy.smartqueue.R

class NotificationHelper(
    private val context: Context
) {
    init {
        createNotificationChannel()
    }

    fun showNearTurnNotification(queueNumber: String, remainingBeforeMe: Int, departmentName: String) {
        showNotification(
            id = queueNumber.hashCode(),
            title = "Sắp đến lượt khám",
            body = "Phiếu $queueNumber còn $remainingBeforeMe lượt nữa. Vui lòng di chuyển gần $departmentName.",
        )
    }

    fun showCallingNotification(queueNumber: String, departmentName: String) {
        showNotification(
            id = queueNumber.hashCode() + 1,
            title = "Đến lượt khám",
            body = "Phiếu $queueNumber đang được gọi. Vui lòng vào $departmentName.",
        )
    }

    private fun showNotification(id: Int, title: String, body: String) {
        if (
            Build.VERSION.SDK_INT >= Build.VERSION_CODES.TIRAMISU &&
            ContextCompat.checkSelfPermission(context, Manifest.permission.POST_NOTIFICATIONS) != PackageManager.PERMISSION_GRANTED
        ) {
            return
        }

        val notification = NotificationCompat.Builder(context, CHANNEL_ID)
            .setSmallIcon(R.mipmap.ic_launcher)
            .setContentTitle(title)
            .setContentText(body)
            .setPriority(NotificationCompat.PRIORITY_HIGH)
            .setAutoCancel(true)
            .build()

        NotificationManagerCompat.from(context).notify(id, notification)
    }

    private fun createNotificationChannel() {
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O) {
            val channel = NotificationChannel(
                CHANNEL_ID,
                "Thông báo hàng đợi",
                NotificationManager.IMPORTANCE_HIGH,
            ).apply {
                description = "Cảnh báo khi bệnh nhân sắp đến lượt hoặc đang được gọi."
            }

            val manager = context.getSystemService(Context.NOTIFICATION_SERVICE) as NotificationManager
            manager.createNotificationChannel(channel)
        }
    }

    companion object {
        const val CHANNEL_ID = "smart_queue_patient_alerts"
    }
}
