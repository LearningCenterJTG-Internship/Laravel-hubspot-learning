<!-- resources/views/create-contact.blade.php -->

<form action="{{ route('upload-contact') }}" method="post">
    @csrf

    <label for="email">Email:</label>
    <input type="email" name="email" required>

    <label for="firstname">First Name:</label>
    <input type="text" name="firstname" required>

    <label for="lastname">Last Name:</label>
    <input type="text" name="lastname" required>

    <label for="phone">Phone:</label>
    <input type="tel" name="phone">

    <label for="company">Company:</label>
    <input type="text" name="company">

    <label for="website">Website:</label>
    <input type="url" name="website">

    <label for="lifecyclestage">Lifecycle Stage:</label>
    <input type="text" name="lifecyclestage">

    <button type="submit">Upload Contact</button>
</form>
