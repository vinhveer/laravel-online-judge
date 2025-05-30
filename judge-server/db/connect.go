package db

import (
	"log"
	"os"

	"github.com/joho/godotenv"
	"gorm.io/driver/mysql"
	"gorm.io/gorm"
)

var DB *gorm.DB

// InitDB khởi tạo kết nối GORM
func InitDB(dsn string) {
	var err error

	if dsn == "" {
		_ = godotenv.Load() // Không cần kiểm tra lỗi nếu không bắt buộc
		dsn = os.Getenv("MYSQL_DSN")
		if dsn == "" {
			log.Fatal("❌ DSN không được để trống (env MYSQL_DSN cũng không có)")
		}
	}

	DB, err = gorm.Open(mysql.Open(dsn), &gorm.Config{})
	if err != nil {
		log.Fatal("❌ Không kết nối được DB:", err)
	}

	log.Println("✅ Kết nối database thành công")
}

// GetDB trả về kết nối hiện tại
func GetDB() *gorm.DB {
	if DB == nil {
		log.Fatal("⚠️ DB chưa được khởi tạo. Gọi InitDB trước.")
	}
	return DB
}
