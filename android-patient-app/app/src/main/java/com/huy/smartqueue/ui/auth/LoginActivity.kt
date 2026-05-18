package com.huy.smartqueue.ui.auth

import android.content.Intent
import android.os.Bundle
import android.view.View
import androidx.activity.viewModels
import androidx.appcompat.app.AppCompatActivity
import androidx.lifecycle.Lifecycle
import androidx.lifecycle.lifecycleScope
import androidx.lifecycle.repeatOnLifecycle
import com.google.android.material.snackbar.Snackbar
import com.huy.smartqueue.R
import com.huy.smartqueue.databinding.ActivityLoginBinding
import com.huy.smartqueue.ui.departments.DepartmentListActivity
import com.huy.smartqueue.viewmodel.AuthViewModel
import kotlinx.coroutines.launch

class LoginActivity : AppCompatActivity() {
    private lateinit var binding: ActivityLoginBinding
    private val viewModel: AuthViewModel by viewModels()

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityLoginBinding.inflate(layoutInflater)
        setContentView(binding.root)

        binding.loginButton.setOnClickListener {
            val email = binding.emailInput.text?.toString()?.trim().orEmpty()
            val password = binding.passwordInput.text?.toString().orEmpty()

            if (email.isBlank() || password.isBlank()) {
                binding.errorText.text = "Vui lòng nhập đầy đủ email và mật khẩu."
                binding.errorText.visibility = View.VISIBLE
                return@setOnClickListener
            }

            setLoading(true)
            binding.errorText.visibility = View.GONE
            viewModel.login(email, password)
        }

        binding.registerButton.setOnClickListener {
            startActivity(Intent(this, RegisterActivity::class.java))
        }

        observeState()
    }

    private fun observeState() {
        lifecycleScope.launch {
            repeatOnLifecycle(Lifecycle.State.STARTED) {
                launch {
                    viewModel.loginState.collect { message ->
                        if (!message.isNullOrBlank()) {
                            setLoading(false)
                            Snackbar.make(binding.root, message, Snackbar.LENGTH_SHORT).show()
                            startActivity(Intent(this@LoginActivity, DepartmentListActivity::class.java))
                            finish()
                        }
                    }
                }

                launch {
                    viewModel.errorState.collect { error ->
                        if (!error.isNullOrBlank()) {
                            setLoading(false)
                            binding.errorText.text = error
                            binding.errorText.visibility = View.VISIBLE
                        }
                    }
                }
            }
        }
    }

    private fun setLoading(isLoading: Boolean) {
        binding.loginProgress.visibility = if (isLoading) View.VISIBLE else View.GONE
        binding.loginButton.isEnabled = !isLoading
        binding.emailInput.isEnabled = !isLoading
        binding.passwordInput.isEnabled = !isLoading
    }
}
