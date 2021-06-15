package Model;

public class ItemResult
{
    public final String text, list, user;
    public int id;
    public final boolean done;

    public ItemResult(String text, int id, String list, String user, boolean done)
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
        return "ItemResult{" +
                "text='" + text + '\'' +
                ", list='" + list + '\'' +
                ", user='" + user + '\'' +
                ", id=" + id +
                ", done=" + done +
                '}';
    }
}
