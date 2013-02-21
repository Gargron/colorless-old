Date.UTCTimestamp = function(o)
{
  if( !(o && o instanceof Date) )
    o = new Date();
  return Date.UTC( o.getUTCFullYear(), o.getUTCMonth(), o.getUTCDate(), o.getUTCHours(), o.getUTCMinutes(), o.getUTCSeconds() ) / 1000;
}

Date.relativeTime = function(ts1,ts2)
{
  if( !ts2 ) ts2 = Date.UTCTimestamp();

  var diff = ts2 - ts1;

  if( diff < 5 )
  {
    return "about 5 sec ago";
  }
  else if( diff < 21 )
  {
    return "about 20 sec ago";
  }
  else if( diff < 60 )
  {
    return "about a minute ago";
  }
  else if( diff < 3600 )
  {
    return Math.floor( diff / 60 ) + " minutes ago";
  }
  else if( diff < 5400 )
  {
    return "about an hour ago";
  }
  else if( diff < 7200 )
  {
    return "about two hours ago";
  }
  else if( diff < 86400 )
  {
    return Math.floor( diff / 3600 ) + " hours ago";
  }
  else if( diff < 180000 )
  {
    return "Yesterday";
  }
  else
  {
    return Math.floor( diff / 86400 ) + " days ago";
  }
}