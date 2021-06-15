package Model;

public class ItemResult
{
    public final String text, id, list, user;
    public final boolean done;

    public ItemResult(String text, String id, String list, String user, boolean done)
    {
        this.text = text;
        this.id = id;
        this.list = list;
        this.user = user;
        this.done = done;
    }

    @Override
    public String toString()
    {
        return "{" +
                "\"text\":\"" + text + "\"," +
                "\"id\":\"" + id + "\"," +
                "\"list\":\"" + list + "\"," +
                "\"user\":\"" + user + "\"," +
                "\"done\":\"" + done + "\"" +
                "}";
    }
}
