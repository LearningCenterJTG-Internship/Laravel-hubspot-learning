<form action="{{ route('user-save') }}" method="get">
    @csrf

    <label for="email">Email:</label>
    <input type="email" name="email" required>

    <label for="name">User Name:</label>
    <input type="text" name="name" required>

    <label for="id">User id:</label>
    <input type="text" name="id" required>


    <button type="submit">Upload User</button>
</form>
