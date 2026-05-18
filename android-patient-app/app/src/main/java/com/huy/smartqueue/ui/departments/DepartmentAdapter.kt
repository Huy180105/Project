package com.huy.smartqueue.ui.departments

import android.view.LayoutInflater
import android.view.ViewGroup
import androidx.recyclerview.widget.RecyclerView
import com.huy.smartqueue.data.model.Department
import com.huy.smartqueue.databinding.ItemDepartmentBinding

class DepartmentAdapter(
    private val onClick: (Department) -> Unit
) : RecyclerView.Adapter<DepartmentAdapter.DepartmentViewHolder>() {
    private val items = mutableListOf<Department>()

    fun submitList(departments: List<Department>) {
        items.clear()
        items.addAll(departments)
        notifyDataSetChanged()
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): DepartmentViewHolder {
        val binding = ItemDepartmentBinding.inflate(LayoutInflater.from(parent.context), parent, false)
        return DepartmentViewHolder(binding)
    }

    override fun onBindViewHolder(holder: DepartmentViewHolder, position: Int) {
        holder.bind(items[position])
    }

    override fun getItemCount(): Int = items.size

    inner class DepartmentViewHolder(
        private val binding: ItemDepartmentBinding
    ) : RecyclerView.ViewHolder(binding.root) {
        fun bind(department: Department) {
            binding.departmentName.text = department.name
            binding.roomNumber.text = department.roomNumber
            binding.currentNumber.text = department.currentNumber ?: "Chưa gọi số"
            binding.averageTime.text = "${department.averageTimePerPatient} phút / bệnh nhân"
            binding.root.setOnClickListener { onClick(department) }
        }
    }
}
