function kickUser(nick) {
  if (nick) {
    $.post("/chat/kick/"+channel, {target: nick});
  }
}