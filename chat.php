<?php
    session_start();
    include 'helper.php';

    if (!authenticate()) {
        header('location: login.php');
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Assignment 3</title>
        <link rel='stylesheet' href='chat.css'>
        <script src="jquery-3.7.1.min.js"></script>
        <script src="chat.js"></script>
        
    </head>
    <body>
        <h1>A Simple Chatroom Service</h1>
        <section id="chatUI">
            <input id="logOutBtn" type="button" value="Logout">
            <div id="messageWindow"></div>
            <form action="" id="chatBox" autocomplete="off">
                <textarea name="message" id="message" cols="30" rows="2" form="chatBox"></textarea>
                <input type="submit" value="SEND" id="sendBtn">
            </form>
        </section>
    </body>
</html>