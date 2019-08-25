#!/bin/bash

# diskSpaceWarning.sh
# Check the available disk space and send a warning if over the threshold

DATE=`date`
SENDER='xxxx@midcenturymaddist.com'
RECIPIENTS='xxxx@gmail.com'

DISKSPACEUSED=`df -h | grep '/dev/xvda1' | awk '{print $5}' | sed 's/%//g'`
DISKSPACELEFT=`df -h | grep '/dev/xvda1' | awk '{print $4}'`

if [ ${DISKSPACEUSED} -gt 79 ] ;
then
  for RECIPIENT in ${RECIPIENTS}
  do
    echo -e "Subject:[Mid Century Maddist] disk space warning \n\n Warning! ${DISKSPACEUSED}% of the disk space is in use. There is only ${DISKSPACELEFT} left." | /usr/sbin/sendmail -f "${SENDER}" "${RECIPIENT}"
  done
fi

