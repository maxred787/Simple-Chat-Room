async function loadMessages() {
    let response = await fetch('chatmsg.php')
    if (response.status == 200) {
        let data = await response.json();
        data.msgs.sort((a, b) => parseInt(a.time) - parseInt(b.time));
        if ($('#messageWindow').children().first().length > 0) {
            var prevLatestMsgid = $('#messageWindow').children().first()[0].getAttribute('id');
        } else {
            var prevLatestMsgid = 'msg-1';
        }
        for (let i = 0; i < data.msgs.length; i++) {
            const id = data.msgs[i].msgid;
            if (parseInt(prevLatestMsgid.replace("msg", '')) < data.msgs[i].msgid) {
                const message = data.msgs[i].message;
                const time = unixStampToTimeString(data.msgs[i].time);
                const person = data.msgs[i].person;
                if (person.match(data.user)) {
                    const messageBox = `\
                        <div id="msg${id}" class="userMessageBox">\
                            <p><b>${person}</b><i>${time}</i></p>\
                            <p class="userMessage">${message}</p>\
                        </div>\
                    `;
                    $('#messageWindow').prepend(messageBox);
                } else {
                    const messageBox = `\
                        <div id="msg${id}" class="otherMessageBox">\
                            <p><b>${person}</b><i>${time}</i></p>\
                            <p class="otherMessage">${message}</p>\
                        </div>\
                    `;
                    $('#messageWindow').prepend(messageBox);
                }
            }
        }
        if ($('#messageWindow').children().first().length > 0) {
            var currLatestMsgid = $('#messageWindow').children().first()[0].getAttribute('id');
        } else {
            var currLatestMsgid = 'msg-1';
        }

        if (prevLatestMsgid !== currLatestMsgid) {
            $('#messageWindow').scrollTop($('#messageWindow')[0].scrollHeight);
        }


    } else if (response.status == 401) {
        window.location.href ='login.php';
    }
}

function unixStampToTimeString(time) {
    let date = new Date(parseInt(time) * 1000);
    let hours = String(date.getHours());
    let minutes = String(date.getMinutes()).padStart(2, '0');
    let seconds = String(date.getSeconds()).padStart(2, '0');
    return hours + ":" + minutes + ":" + seconds;
}

$(document).ready(_ => {
    $("#logOutBtn").on('click', async e => {
        await fetch('login.php?action=signout');
        window.location.reload();
    });

    $("#chatBox").on('submit', async e => {
        e.preventDefault();
        if ($("#message").length > 0) {
            const message = $("#message")[0].value;
            if (message.length > 0) {
                let init = {
                    method: 'POST',
                    body: "message="+message,
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                    };

                let response = await fetch('chatmsg.php', init);
                if (response.status == 200) {
                    let data = await response.json();
                    if (data.result) {
                        $("#message")[0].value="";
                        clearInterval(timer);
                        loadMessages();
                        timer = setInterval(loadMessages, 5000);
                    }
                }
            }
        }
    })

    loadMessages();
    var timer = setInterval(loadMessages, 5000);
})