# Script để đơn giản hóa tên hàm trong controllers và services

# 1. Tạo BaseController pattern cho tất cả controllers
# 2. Đổi tên hàm từ getXXX, createXXX, updateXXX, deleteXXX thành get, create, update, delete
# 3. Sử dụng BaseController.handleResponse() và handleListResponse()

# Pattern cần áp dụng:
# Controller:
# - extends BaseController<any>
# - protected service = this.xxxService
# - async list() thay vì getXXXs()
# - async get() thay vì getXXX()
# - async create() thay vì createXXX()
# - async update() thay vì updateXXX()
# - async delete() thay vì deleteXXX()

# Service:
# - async list() thay vì getXXXs()
# - async get() thay vì getXXX()
# - async create() thay vì createXXX()
# - async update() thay vì updateXXX()
# - async delete() thay vì deleteXXX()

echo "Pattern đã được áp dụng cho permissions module"
echo "Cần áp dụng cho tất cả modules còn lại"
