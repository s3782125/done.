package Controller;

import Model.ReminderResult;
import com.amazonaws.auth.AWSStaticCredentialsProvider;
import com.amazonaws.auth.BasicAWSCredentials;
import com.amazonaws.regions.Regions;
import com.amazonaws.services.dynamodbv2.AmazonDynamoDBClient;
import com.amazonaws.services.dynamodbv2.AmazonDynamoDBClientBuilder;
import com.amazonaws.services.dynamodbv2.model.*;

import java.util.HashMap;
import java.util.List;
import java.util.Map;

public class MyDynamo
{
    AmazonDynamoDBClient dbClient;

    public MyDynamo()
    {
        dbClient = (AmazonDynamoDBClient) AmazonDynamoDBClientBuilder.standard()
                .withCredentials(
                        new AWSStaticCredentialsProvider(
                                new BasicAWSCredentials(
                                        /* credentials key */,
                                        /* credentials secret */
                                )))
                .withRegion(Regions./* region */)
                .build();
    }

    public List<Map<String, AttributeValue>> scan(String tableName)
    {
        return dbClient.scan(new ScanRequest(tableName)).getItems();
    }

    public void markDone(ReminderResult reminder)
    {
        int id = reminder.id;
        String time = reminder.time.toString();
        String email = reminder.email;

        Map<String, AttributeValue> key = new HashMap<>();
        AttributeValue idValue = new AttributeValue();
        AttributeValue timeValue = new AttributeValue();
        idValue.setN(String.valueOf(id));
        timeValue.setS(time);
        key.put("id", idValue);
        key.put("time", timeValue);

        Map<String, AttributeValueUpdate> update = new HashMap<>();
        AttributeValue doneValue = new AttributeValue();
        doneValue.setBOOL(true);
        AttributeValueUpdate valueUpdate = new AttributeValueUpdate();
        valueUpdate.setValue(doneValue);
        update.put("done", valueUpdate);

        dbClient.updateItem("Reminders", key, update);
    }
}
