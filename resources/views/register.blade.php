<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
</head>
<body>

    <h2>Register</h2>

    <form method="POST" action="/register">
        @csrf

        <input type="text" name="nama_lengkap" placeholder="Nama Lengkap" required>
        <br><br>

        <input type="email" name="email" placeholder="Email" required>
        <br><br>

        <input type="password" name="password" placeholder="Password" required>
        <br><br>

        <button type="submit">Register</button>
    </form>

    <br>

    <a href="/login">Sudah punya akun?</a>

</body>
</html>