const mysql = require('mysql2/promise');
require('dotenv').config();

const dbConfig = {
    host: process.env.DB_HOST,
    user: process.env.DB_USER,
    password: process.env.DB_PASSWORD,
    database: process.env.DB_NAME,
    port: process.env.DB_PORT
};

class Database {
    static async getConnection() {
        try {
            const connection = await mysql.createConnection(dbConfig);
            return connection;
        } catch (error) {
            console.error('Error conectando a la base de datos:', error);
            throw error;
        }
    }

    static async query(sql, params = []) {
        let connection;
        try {
            connection = await this.getConnection();
            const [results] = await connection.execute(sql, params);
            return results;
        } catch (error) {
            console.error('Error ejecutando query:', error);
            throw error;
        } finally {
            if (connection) {
                await connection.end();
            }
        }
    }
}

module.exports = Database;
