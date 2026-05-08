<h1>Edit Health Log</h1>

<form action="{{ route('health-logs.update', $healthLog->id) }}"
      method="POST">

    @csrf
    @method('PUT')

    <input type="number"
           name="heart_rate"
           value="{{ $healthLog->heart_rate }}">

    <input type="number"
           name="sleep_hours"
           value="{{ $healthLog->sleep_hours }}">

    <input type="number"
           name="water_intake"
           value="{{ $healthLog->water_intake }}">

    <input type="number"
           name="calories"
           value="{{ $healthLog->calories }}">

    <textarea name="symptoms">{{ $healthLog->symptoms }}</textarea>

    <input type="text"
           name="mood"
           value="{{ $healthLog->mood }}">

    <button type="submit">
        Update
    </button>

</form>