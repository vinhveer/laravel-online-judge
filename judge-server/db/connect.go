package db

import (
	"log"
	"os"

	"github.com/joho/godotenv"
	"gorm.io/driver/mysql"
	"gorm.io/gorm"
)

var DB *gorm.DB

func InitDB(dsn string) {
	var err error
	if dsn == "" {
		// Load .env file if it exists
		_ = godotenv.Load()
		dsn = os.Getenv("MYSQL_DSN")
		if dsn == "" {
			log.Fatal("DSN không được để trống")
		}
	}

	DB, err = gorm.Open(mysql.Open(dsn), &gorm.Config{})
	if err != nil {
		log.Fatal("Không kết nối được DB:", err)
	}
}

func GetDB() *gorm.DB {
	return DB
}
