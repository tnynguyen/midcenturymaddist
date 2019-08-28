#!/bin/bash

# websiteChecker.sh
# Check to make sure we receive a valid HTTP response from the website
# send a notification email if anything other than a 200 or 000 response is received

DATE=`date`
WEBSITE='https://www.midcenturymaddist.com/'
RESPONSE=`curl --url ${WEBSITE} --write-out %{http_code} --silent --output /dev/null`
CACHEFILE=/tmp/websiteChecker.tmp
SENDER='xxxx@midcenturymaddist.com'
RECIPIENTS='xxxx@midcenturymaddist.com xxxx@gmail.com'

#valid http response codes ... 200 ok, 206 partial content, 000 client abort
if [ ${RESPONSE} -eq 200 -o ${RESPONSE} -eq 000 ] ;
then
  #website is up
  if [ -f ${CACHEFILE} ] ;
  then
    #remove tmp file if exists and notify recipients
    for RECIPIENT in ${RECIPIENTS}
    do
      echo -e "Subject:[Mid Century Maddist] website is up! \n\n Hooray! The website is up and running with a received response code of ${RESPONSE}." | /usr/sbin/sendmail -f "${SENDER}" "${RECIPIENT}" 
    done
    rm -f ${CACHEFILE}
  fi
else
  #website is down
  if [ ! -f ${CACHEFILE} ] ;
  then
    #notify recipients and create a tmp file so only one email is sent
    for RECIPIENT in ${RECIPIENTS}
    do
      echo -e "Subject:[Mid Century Maddist] website is down! \n\n Oh no! The website is down with a received response code of ${RESPONSE}." | /usr/sbin/sendmail -f "${SENDER}" "${RECIPIENT}"
    done
    touch ${CACHEFILE}
  fi
fi

