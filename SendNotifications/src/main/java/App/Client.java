package App;

import Controller.Credentials;
import Controller.Mail;
import Controller.MyDynamo;
import Model.ItemResult;
import Model.ReminderResult;
import com.amazonaws.services.dynamodbv2.model.AttributeValue;
import com.google.api.services.gmail.Gmail;
import com.google.api.services.gmail.model.Message;

import javax.mail.MessagingException;
import java.io.IOException;
import java.security.GeneralSecurityException;
import java.time.LocalDateTime;
import java.time.ZoneId;
import java.time.ZonedDateTime;
import java.time.format.DateTimeFormatter;
import java.time.format.DateTimeParseException;
import java.util.HashMap;
import java.util.Map;
import java.util.PriorityQueue;
import java.util.Queue;

public class Client
{
    /**
     * 15 mins in nanoseconds
     */
    private static final double waitTime = 9 * Math.pow(10, 11);

    public static void main(String[] args) throws GeneralSecurityException, IOException,
            InterruptedException
    {
        Gmail service = Credentials.getGmail();
        MyDynamo ddb = new MyDynamo();

        while (true)
        {
            // Get all items and put them into this map
            Map<Integer, ItemResult> itemResults = new HashMap<>();
            for (Map<String, AttributeValue> item : ddb.scan("ListItems"))
            {
                String text = "";
                if (item.containsKey("text"))
                    text = item.get("text").getS();
                int id = Integer.parseInt(item.get("id").getN());
                String itemUser = item.get("user").getS();
                String list = item.get("list").getS();
                boolean done = item.get("done").getBOOL();

                itemResults.put(id, new ItemResult(text, id, list, itemUser, done));
            }

            // Get all reminders and put them into this priority queue
            Queue<ReminderResult> reminders = new PriorityQueue<>();
            for (Map<String, AttributeValue> map : ddb.scan("Reminders"))
            {
                String email = map.get("email").getS();
                int id = Integer.parseInt(map.get("id").getN());
                try
                {
                    boolean done = map.get("done").getBOOL();
                    LocalDateTime time = LocalDateTime.parse(map.get("time").getS());

                    if (!done)
                        reminders.add(new ReminderResult(email, time, id, false));

                } catch (DateTimeParseException e)
                {
                    System.err.printf("Notification for email: %s id: %s has an invalid date\n",
                            email, id);
                }
            }


            ReminderResult reminder = reminders.poll();
            double breakTime = System.nanoTime() + waitTime;
            double now = System.nanoTime();
            while (now < breakTime)
            {
                System.out.printf("Time until next query: %.0f secs\n",
                        (breakTime - now) / (1 * Math.pow(10, 9)));

                DateTimeFormatter formatter = DateTimeFormatter.ofPattern("yyyy-MM-dd HH:mm:ss");
                ZoneId AEST = ZoneId.of("Australia/Sydney");

                ZonedDateTime time =
                        ZonedDateTime.parse(reminder.time + "+10:00[" + AEST + "]");
                ZonedDateTime nowTime = ZonedDateTime.now(AEST);

                System.out.printf("The current time is %s, next email is at %s\n\n",
                        nowTime.format(formatter), time.format(formatter));

                if (nowTime.isAfter(time))
                {
                    ItemResult item = itemResults.get(reminder.id);

                    System.out.printf("\nSending email to %s...\n", reminder.email);
                    String subject = "Don't Forget!";

                    StringBuilder body = new StringBuilder(item.text);
                    body.append("\nYou scheduled this for: ").append(time.format(formatter));
                    if (item.done)
                        body.append("\nThis item is actually already done, Congrats!");
                    body.append("\n\nThis is an automated email, please don't respond.\n- The " +
                            "done. team");

                    try
                    {
                        Message message = Mail.sendEmail(service, reminder.email, subject,
                                body.toString());
                        System.out.println(message.toPrettyString());
                        ddb.markDone(reminder);
                        System.out.println("Database updated\n");

                        reminder = reminders.poll();

                    } catch (MessagingException e)
                    {
                        e.printStackTrace();
                    }
                } else
                {
                    // Wait 30 seconds if there is nothing to send
                    Thread.sleep(30000);
                }
                now = System.nanoTime();
            }
        }
    }
}
