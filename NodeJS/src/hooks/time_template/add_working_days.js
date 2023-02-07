import { checkContext, getItems, replaceItems } from 'feathers-hooks-common/lib/utils'

function calculateNumberOfWorkingDays (timeTemplate) {
  timeTemplate.numberOfWorkingDays = timeTemplate.workingHours.reduce((sum, { timeRanges }) => {
    return sum + (timeRanges.length > 0 ? 1 : 0)
  }, 0)

  return timeTemplate
}

export default function addWorkingDays (hook) {
  checkContext(hook, 'after')

  let timeTemplates = getItems(hook)

  if (Array.isArray(timeTemplates)) {
    timeTemplates = timeTemplates.map(calculateNumberOfWorkingDays)
  } else {
    timeTemplates = calculateNumberOfWorkingDays(timeTemplates)
  }

  replaceItems(hook, timeTemplates)
  return hook
}
