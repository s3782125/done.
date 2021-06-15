package App;

import Controller.Database;
import Controller.MyDynamo;
import Model.Response;
import com.amazonaws.services.dynamodbv2.model.AttributeValue;
import com.amazonaws.services.lambda.runtime.Context;
import com.amazonaws.services.lambda.runtime.RequestHandler;

import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.ArrayList;
import java.util.LinkedHashMap;
import java.util.List;
import java.util.Map;

public class Handler implements RequestHandler<LinkedHashMap<String, Object>, Response>
{
    public Response handleRequest(LinkedHashMap<String, Object> input, Context context)
    {
        StringBuilder sb = new StringBuilder("{");
        String user;

        LinkedHashMap<String, String> query;
        query = (LinkedHashMap<String, String>) input.get("queryStringParameters");

        if (query.containsKey("user") && query.containsKey("pass"))
        {
            Database db = new Database();
            user = query.get("user");
            String pass = query.get("pass");

            ResultSet resultSet = db.query("SELECT * FROM users");
            try
            {
                boolean userFound = false;
                while (resultSet.next())
                {
                    if (resultSet.getString("username").equals(user)
                            && resultSet.getString("password").equals(pass))
                    {
                        sb.append("\"user\":\"").append(user).append("\",");
                        userFound = true;
                        break;
                    }
                }
                if (!userFound)
                    return new Response("{\"ErrorMessage\":\"Requested user not found, or " +
                            "incorrect password was given\"}", 417);

            } catch (SQLException e)
            {
                e.printStackTrace();
            }

        } else
            return new Response("{\"ErrorMessage\":\"User or password not specified, unable to " +
                    "process query\"}", 417);

        List<String> lists = new ArrayList<>();
        for (Map<String, AttributeValue> item : new MyDynamo().scan())
        {
            if (item.get("user").getS().equals(user))
            {
                for (AttributeValue value : item.get("lists").getL())
                    lists.add(value.getS());
                break;
            }
        }

        int i = 0;
        sb.append("\"lists\":[");
        for (String list : lists)
        {
            sb.append("\"").append(list).append("\"");
            if (i != lists.size() - 1)
            {
                sb.append(",");
                i++;
            }
        }
        sb.append("]").append("}");

        return new Response(sb.toString(), 200);
    }
}
