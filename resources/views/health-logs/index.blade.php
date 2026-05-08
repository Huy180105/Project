<h1>Health Logs</h1>

<a href="{{ route('health-logs.create') }}">
    Add Health Log
</a>

<hr>

@foreach($healthLogs as $log)

    <div style="margin-bottom:20px; border:1px solid #ccc; padding:10px;">

        <p>Heart Rate: {{ $log->heart_rate }}</p>

        <p>Sleep Hours: {{ $log->sleep_hours }}</p>

        <p>Water Intake: {{ $log->water_intake }}</p>

        <p>Calories: {{ $log->calories }}</p>

        <p>Symptoms: {{ $log->symptoms }}</p>

        <p>Mood: {{ $log->mood }}</p>

        <a href="{{ route('health-logs.edit', $log->id) }}">
            Edit
        </a>

        <form action="{{ route('health-logs.destroy', $log->id) }}"
              method="POST">

            @csrf
            @method('DELETE')

            <button type="submit">
                Delete
            </button>

        </form>

    </div>

@endforeach