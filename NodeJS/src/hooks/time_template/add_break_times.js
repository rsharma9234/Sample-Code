import _ from 'lodash'
import { checkContext, getItems, replaceItems } from 'feathers-hooks-common/lib/utils'

function calculateBreakTimes (timeTemplate) {
  if (_.isNil(timeTemplate.workingHours)) {
    return timeTemplate
  }

  const workingHoursWithBreaks = timeTemplate.workingHours.map(({ timeRanges }) => {
    if (timeRanges.length <= 1) {
      return { timeRanges, breakTimes: [] }
    }

    const breakTimes = []
    let breakStart, breakEnd

    for (let i = 0; i < timeRanges.length; i++) {
      if (i === 0) {
        breakStart = timeRanges[i].to
        continue
      }

      breakEnd = timeRanges[i].from
      breakTimes.push({ from: breakStart, to: breakEnd })

      breakStart = timeRanges[i].to
    }

    return { timeRanges, breakTimes }
  })

  return { ...timeTemplate, workingHours: workingHoursWithBreaks }
}

export default function addBreakTimes (hook) {
  checkContext(hook, 'before', [ 'create', 'patch', 'update' ])

  let timeTemplates = getItems(hook)

  if (Array.isArray(timeTemplates)) {
    timeTemplates = timeTemplates.map(calculateBreakTimes)
  } else {
    timeTemplates = calculateBreakTimes(timeTemplates)
  }

  replaceItems(hook, timeTemplates)

  return hook
}
