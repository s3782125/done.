package App;

import Controller.Database;
import Controller.MyDynamo;
import Model.ItemResult;
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
import java.util.stream.Collectors;

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

        if (query.containsKey("list"))
        {
            String listName = query.get("list");

            List<ItemResult> itemResultList = getItemResults(user).stream()
                    .filter(item -> item.list.equals(listName)).collect(Collectors.toList());
            if (itemResultList.isEmpty())
            {
                return new Response("{\"ErrorMessage\":\"The requested list doesn't exist or is " +
                        "empty\"}", 417);
            }

            sb.append("\"list\":\"").append(listName).append("\",").append("\"items\":[");

            for (ItemResult item : itemResultList)
                sb.append(item).append(",");
            sb.deleteCharAt(sb.length() - 1);
            sb.append("]");

        } else
        {
            return new Response("{\"ErrorMessage\":\"No list specified, unable to process query\"}",
                    417);
        }
        sb.append("}");

        return new Response(sb.toString(), 200);
    }

    private List<ItemResult> getItemResults(String user)
    {
        MyDynamo dynamo = new MyDynamo();

        List<ItemResult> itemResults = new ArrayList<>();
        for (Map<String, AttributeValue> item : dynamo.scan())
        {
            String text = item.get("text").getS();
            String id = item.get("id").getN();
            String itemUser = item.get("user").getS();
            String list = item.get("list").getS();
            boolean done = item.get("done").getBOOL();

            if (itemUser.equals(user))
                itemResults.add(new ItemResult(text, id, list, itemUser, done));
        }
        return itemResults;
    }
}
