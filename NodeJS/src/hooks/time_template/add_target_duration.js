import _ from 'lodash'
import moment from 'moment-timezone'
import { checkContext, getItems, replaceItems } from 'feathers-hooks-common/lib/utils'

function calculateDuration (timeTemplate) {
  if (_.isNil(timeTemplate.workingHours)) {
    return timeTemplate
  }

  timeTemplate.workingHours = timeTemplate.workingHours.map((dailyWorkingHours) => {
    const duration = dailyWorkingHours.timeRanges.reduce((total, timeRanges) => {
      const from = moment({
        hours: timeRanges.from.hour,
        minutes: timeRanges.from.minute
      })
      const to = moment({
        hours: timeRanges.to.hour,
        minutes: timeRanges.to.minute
      })

      return total + to.diff(from, 'minutes')
    }, 0)

    dailyWorkingHours.targetDuration = duration
    return dailyWorkingHours
  })

  return timeTemplate
}

export default function addTargetDuration (hook) {
  checkContext(hook, 'before', [ 'create', 'patch', 'update' ])

  let timeTemplates = getItems(hook)

  if (Array.isArray(timeTemplates)) {
    timeTemplates = timeTemplates.map(calculateDuration)
  } else {
    timeTemplates = calculateDuration(timeTemplates)
  }

  replaceItems(hook, timeTemplates)
  return hook
}
