package com.huy.smartqueue.ui.departments

import android.content.Intent
import android.os.Bundle
import android.view.View
import androidx.activity.viewModels
import androidx.appcompat.app.AppCompatActivity
import androidx.lifecycle.Lifecycle
import androidx.lifecycle.lifecycleScope
import androidx.lifecycle.repeatOnLifecycle
import androidx.recyclerview.widget.LinearLayoutManager
import com.huy.smartqueue.databinding.ActivityDepartmentListBinding
import com.huy.smartqueue.ui.ticket.CreateTicketActivity
import com.huy.smartqueue.ui.ticket.MyTicketActivity
import com.huy.smartqueue.viewmodel.QueueViewModel
import kotlinx.coroutines.launch

class DepartmentListActivity : AppCompatActivity() {
    private lateinit var binding: ActivityDepartmentListBinding
    private val viewModel: QueueViewModel by viewModels()
    private var hasRequestedMyTicket = false
    private val adapter = DepartmentAdapter { department ->
        startActivity(CreateTicketActivity.intent(this, department.id, department.name, department.roomNumber))
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityDepartmentListBinding.inflate(layoutInflater)
        setContentView(binding.root)

        binding.departmentRecycler.layoutManager = LinearLayoutManager(this)
        binding.departmentRecycler.adapter = adapter
        binding.myTicketButton.setOnClickListener {
            hasRequestedMyTicket = true
            viewModel.loadMyTicket()
        }

        observeState()
        viewModel.loadDepartments()
    }

    private fun observeState() {
        lifecycleScope.launch {
            repeatOnLifecycle(Lifecycle.State.STARTED) {
                launch {
                    viewModel.departmentsState.collect { departments ->
                        adapter.submitList(departments)
                        binding.emptyText.visibility = if (departments.isEmpty()) View.VISIBLE else View.GONE
                    }
                }

                launch {
                    viewModel.loadingState.collect { isLoading ->
                        binding.loadingProgress.visibility = if (isLoading) View.VISIBLE else View.GONE
                    }
                }

                launch {
                    viewModel.errorState.collect { error ->
                        binding.errorText.text = error
                        binding.errorText.visibility = if (error.isNullOrBlank()) View.GONE else View.VISIBLE
                    }
                }

                launch {
                    viewModel.myTicketLoading.collect { isLoading ->
                        binding.myTicketButton.isEnabled = !isLoading
                    }
                }

                launch {
                    viewModel.myTicketError.collect { error ->
                        binding.myTicketMessage.text = error
                        binding.myTicketMessage.visibility = if (error.isNullOrBlank()) View.GONE else View.VISIBLE
                    }
                }

                launch {
                    viewModel.myTicketState.collect { ticket ->
                        if (ticket != null) {
                            startActivity(MyTicketActivity.intent(this@DepartmentListActivity, ticket))
                        } else if (hasRequestedMyTicket && !viewModel.myTicketLoading.value) {
                            binding.myTicketMessage.text = "Bạn chưa có phiếu khám đang hoạt động"
                            binding.myTicketMessage.visibility = View.VISIBLE
                        }
                    }
                }
            }
        }
    }
}
