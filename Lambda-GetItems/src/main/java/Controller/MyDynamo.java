package Controller;

import com.amazonaws.auth.AWSStaticCredentialsProvider;
import com.amazonaws.auth.BasicAWSCredentials;
import com.amazonaws.regions.Regions;
import com.amazonaws.services.dynamodbv2.AmazonDynamoDBClient;
import com.amazonaws.services.dynamodbv2.AmazonDynamoDBClientBuilder;
import com.amazonaws.services.dynamodbv2.model.AttributeValue;
import com.amazonaws.services.dynamodbv2.model.ScanRequest;

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

    public List<Map<String, AttributeValue>> scan()
    {
        return dbClient.scan(new ScanRequest("ListItems")).getItems();
    }
}
