<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Selection</title>
  </head>
  <body>
    <form method="post">

      <label for="year">Select the year:</label>
      <select multiple="multiple" name="year">
        <option value="2019">2019</option>
        <option value="2020">2020</option>
      </select>

    <label for="month">Select the month</label>
    <select multiple="multiple" name="month">
      <option value="2018">2018</option>
      <option value="2019">2019</option>
      <option value="2020">2020</option>
    </select>

  <label for="day">Select the day</label>
  <select multiple="multiple" name="day">
    <option value="MONDAY">Monday</option>
    <option value="TUESDAY">Tuesday</option>
    <option value="WEDNESDAY">Wednesday</option>
    <option value="THURSDAY">Thursday</option>
    <option value="FRIDAY">Friday</option>
    <option value="SATURDAY">Saturday</option>
    <option value="SUNDAY">Sunday</option>
  </select>

  <label for="hour">Select the hour</label>
  <select multiple="multiple" name="hour">
    <option value="24:00-01:00">24:00-01:00</option>
    <option value="01:00-02:00">01:00-02:00</option>
    <option value="02:00-03:00">02:00-03:00</option>
    <option value="03:00-04:00">03:00-04:00</option>
    <option value="04:00-05:00">04:00-05:00</option>
    <option value="05:00-06:00">05:00-06:00</option>
    <option value="06:00-07:00">06:00-07:00</option>
    <option value="07:00-08:00">07:00-08:00</option>
    <option value="08:00-09:00">08:00-09:00</option>
  </select>

  <label for="activity">Select the activity:</label>
  <select multiple="multiple" name="activity">
    <option value="WALKING">Walking</option>
    <option value="STILL">Still</option>
    <option value="DRIVING">Driving</option>
  </select>
  <button type="submit" name="submit" value="submit">Draw map</button>
  </form>

  </body>
</html>
