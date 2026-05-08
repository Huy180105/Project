<h1>Create Health Log</h1>

<form action="{{ route('health-logs.store') }}" method="POST">

    @csrf

    <input type="number" name="heart_rate" placeholder="Heart Rate">

    <input type="number" name="sleep_hours" placeholder="Sleep Hours">

    <input type="number" name="water_intake" placeholder="Water Intake">

    <input type="number" name="calories" placeholder="Calories">

    <textarea name="symptoms" placeholder="Symptoms"></textarea>

    <input type="text" name="mood" placeholder="Mood">

    <button type="submit">
        Save
    </button>

</form>