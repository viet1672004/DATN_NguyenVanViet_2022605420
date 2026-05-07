export default {
  vi: {
    booking: {
      title: 'Đặt vé',
      list: 'Danh sách booking',
      create: 'Tạo booking',
      edit: 'Sửa booking',

      fields: {
        id: 'ID',
        user: 'Người dùng',
        booking_date: 'Ngày đặt',
        total_price: 'Tổng tiền',
        status: 'Trạng thái',
        ticket: 'Vé',
        quantity: 'Số lượng',
        price: 'Giá'
      },

      actions: {
        create: 'Thêm mới',
        edit: 'Sửa',
        delete: 'Xoá',
        save: 'Lưu',
        cancel: 'Huỷ'
      },

      message: {
        confirm_delete: 'Bạn có chắc muốn xoá?',
        create_success: 'Tạo thành công',
        update_success: 'Cập nhật thành công',
        delete_success: 'Xoá thành công'
      }
    }
  },

  en: {
    booking: {
      title: 'Booking',
      list: 'Booking List',
      create: 'Create Booking',
      edit: 'Edit Booking',

      fields: {
        id: 'ID',
        user: 'User',
        booking_date: 'Booking Date',
        total_price: 'Total Price',
        status: 'Status',
        ticket: 'Ticket',
        quantity: 'Quantity',
        price: 'Price'
      },

      actions: {
        create: 'Create',
        edit: 'Edit',
        delete: 'Delete',
        save: 'Save',
        cancel: 'Cancel'
      },

      message: {
        confirm_delete: 'Are you sure to delete?',
        create_success: 'Created successfully',
        update_success: 'Updated successfully',
        delete_success: 'Deleted successfully'
      }
    }
  }
}