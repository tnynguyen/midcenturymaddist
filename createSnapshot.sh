#!/bin/bash

# createSnapshot.sh
# This shell script creates an AWS snapshot and sends a notification email
# requires the aws cli and sendmail utility to be installed with a valid profile configured

DATE=`date +'%Y-%m-%d'`
CACHEFILE=/tmp/createSnapshot.tmp
SENDER='info@midcenturymaddist.com'
RECIPIENTS='info@midcenturymaddist.com tnynguyen@gmail.com'
PROFILE='xxxxxxx'
OWNERID='000000000000'
VOLID='vol-00000000000000'

#record the old snapshot id for cleanup
OLDSNAP=`aws ec2 describe-snapshots --profile "${PROFILE}" --owner-ids "${OWNERID}" --filters Name=volume-id,Values="${VOLID}" Name=status,Values=completed | grep SnapshotId | tail -1 | awk -F\" '{print $4}'`

#create the new snapshot
NEWSNAP=`aws ec2 create-snapshot --profile "${PROFILE}" --volume-id "${VOLID}" --description "midcenturymaddist-${DATE}"`

#sleep 60 seconds to let the new snapshot finish
sleep 60s

#get the number of completed snapshots
COMPLETEDCOUNT=`aws ec2 describe-snapshots --profile "${PROFILE}" --owner-ids "${OWNERID}" --filters Name=volume-id,Values="${VOLID}" Name=status,Values=completed | grep SnapshotId | wc -l`

if [ ${COMPLETEDCOUNT} -eq 2 ] ;
then
  #remove the old snapshot
  aws ec2 delete-snapshot --profile "${PROFILE}" --snapshot-id "${OLDSNAP}"
  for RECIPIENT in ${RECIPIENTS}
  do
    ( echo "Subject:[Mid Century Maddist] backup successful!"; echo; echo "Well whadya know, a new snapshot backup was successfully taken:"; echo; echo "${NEWSNAP}"; echo; echo "The old backup with SnapshotId ${OLDSNAP} was terminated. Hasta la vista, baby." ) | /usr/sbin/sendmail -f "${SENDER}" "${RECIPIENT}"
  done
else
  for RECIPIENT in ${RECIPIENTS}
  do
    ( echo "Subject:[Mid Century Maddist] backup issue!"; echo; echo "Dagnabbit! There was an issue with the snapshot backup just now. The good news is that we still have the last successful snapshot backup available."; echo; echo "${NEWSNAP}" ) | /usr/sbin/sendmail -f "${SENDER}" "${RECIPIENT}"
  done
fi

