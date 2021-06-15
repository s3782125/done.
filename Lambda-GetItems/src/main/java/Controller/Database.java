package Controller;

import java.sql.*;

public class Database
{
    Connection conn;

    public Database()
    {
        try
        {
            conn = DriverManager.getConnection("jdbc:mysql:///* RDS address */:3306/db?user=/* RDS username */&password=/* RDS password */");
        } catch (SQLException throwables)
        {
            throwables.printStackTrace();
        }
    }

    public ResultSet query(String sql)
    {
        ResultSet resultSet = null;

        try
        {
            PreparedStatement st = conn.prepareStatement(sql);
            resultSet = st.executeQuery();
        } catch (SQLException e)
        {
            e.printStackTrace();
        }

        return resultSet;
    }
}
