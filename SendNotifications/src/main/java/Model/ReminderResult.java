package Model;

import java.time.LocalDateTime;

public class ReminderResult implements Comparable<ReminderResult>
{
    public final String email;
    public final LocalDateTime time;
    public final int id;
    public final boolean done;

    public ReminderResult(String email, LocalDateTime time, int id, boolean done)
    {
        this.email = email;
        this.time = time;
        this.id = id;
        this.done = done;
    }

    @Override
    public String toString()
    {
        return "ReminderResult{" +
                "email='" + email + '\'' +
                ", time=" + time +
                ", id=" + id +
                ", done=" + done +
                '}';
    }

    @Override
    public int compareTo(ReminderResult o)
    {
        return this.time.compareTo(o.time);
    }
}
