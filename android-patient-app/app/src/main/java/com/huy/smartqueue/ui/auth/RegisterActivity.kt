package com.huy.smartqueue.ui.auth

import android.content.Intent
import android.os.Bundle
import android.view.View
import android.widget.ArrayAdapter
import androidx.activity.viewModels
import androidx.appcompat.app.AppCompatActivity
import androidx.lifecycle.Lifecycle
import androidx.lifecycle.lifecycleScope
import androidx.lifecycle.repeatOnLifecycle
import com.google.android.material.snackbar.Snackbar
import com.huy.smartqueue.data.model.RegisterRequest
import com.huy.smartqueue.databinding.ActivityRegisterBinding
import com.huy.smartqueue.ui.departments.DepartmentListActivity
import com.huy.smartqueue.viewmodel.AuthViewModel
import kotlinx.coroutines.launch

class RegisterActivity : AppCompatActivity() {
    private lateinit var binding: ActivityRegisterBinding
    private val viewModel: AuthViewModel by viewModels()

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityRegisterBinding.inflate(layoutInflater)
        setContentView(binding.root)

        val genderOptions = listOf("male", "female", "other")
        binding.genderInput.setAdapter(ArrayAdapter(this, android.R.layout.simple_list_item_1, genderOptions))

        binding.registerButton.setOnClickListener {
            val request = collectRequest()
            if (request == null) {
                binding.errorText.text = "Vui lòng nhập đủ họ tên, email, mật khẩu và số điện thoại."
                binding.errorText.visibility = View.VISIBLE
                return@setOnClickListener
            }

            binding.errorText.visibility = View.GONE
            viewModel.register(request)
        }

        observeState()
    }

    private fun collectRequest(): RegisterRequest? {
        val name = binding.nameInput.text?.toString()?.trim().orEmpty()
        val email = binding.emailInput.text?.toString()?.trim().orEmpty()
        val password = binding.passwordInput.text?.toString().orEmpty()
        val confirmPassword = binding.confirmPasswordInput.text?.toString().orEmpty()
        val phone = binding.phoneInput.text?.toString()?.trim().orEmpty()

        if (name.isBlank() || email.isBlank() || password.isBlank() || confirmPassword.isBlank() || phone.isBlank()) {
            return null
        }

        return RegisterRequest(
            name = name,
            email = email,
            password = password,
            password_confirmation = confirmPassword,
            phone = phone,
            dob = binding.dobInput.text?.toString()?.trim().nullIfBlank(),
            gender = binding.genderInput.text?.toString()?.trim().nullIfBlank(),
            insurance_number = binding.insuranceInput.text?.toString()?.trim().nullIfBlank(),
            citizen_id = binding.citizenIdInput.text?.toString()?.trim().nullIfBlank(),
            address = binding.addressInput.text?.toString()?.trim().nullIfBlank(),
            emergency_contact_name = binding.emergencyNameInput.text?.toString()?.trim().nullIfBlank(),
            emergency_contact_phone = binding.emergencyPhoneInput.text?.toString()?.trim().nullIfBlank(),
            medical_history = binding.medicalHistoryInput.text?.toString()?.trim().nullIfBlank(),
            allergies = binding.allergiesInput.text?.toString()?.trim().nullIfBlank(),
        )
    }

    private fun observeState() {
        lifecycleScope.launch {
            repeatOnLifecycle(Lifecycle.State.STARTED) {
                launch {
                    viewModel.registerLoading.collect { loading ->
                        binding.registerProgress.visibility = if (loading) View.VISIBLE else View.GONE
                        binding.registerButton.isEnabled = !loading
                    }
                }
                launch {
                    viewModel.registerError.collect { error ->
                        binding.errorText.text = error
                        binding.errorText.visibility = if (error.isNullOrBlank()) View.GONE else View.VISIBLE
                    }
                }
                launch {
                    viewModel.registerState.collect { message ->
                        if (!message.isNullOrBlank()) {
                            Snackbar.make(binding.root, message, Snackbar.LENGTH_SHORT).show()
                            startActivity(Intent(this@RegisterActivity, DepartmentListActivity::class.java))
                            finish()
                        }
                    }
                }
            }
        }
    }

    private fun String?.nullIfBlank(): String? = this?.takeIf { it.isNotBlank() }
}
