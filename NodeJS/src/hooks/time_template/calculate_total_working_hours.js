import { checkContext, getItems, replaceItems } from 'feathers-hooks-common/lib/utils'

function totalWorkingHours (timeTemplate) {
  timeTemplate.totalWorkingHours = timeTemplate.workingHours.reduce(
    (total, o) => total + o.targetDuration,
    0
  )

  return timeTemplate
}

export default function calculateTotalWorkingHours (hook) {
  checkContext(hook, 'before', [ 'create', 'update', 'patch' ])

  let timeTemplates = getItems(hook)
  if (Array.isArray(timeTemplates)) {
    timeTemplates = timeTemplates.map(totalWorkingHours)
  } else {
    timeTemplates = totalWorkingHours(timeTemplates)
  }

  replaceItems(hook, timeTemplates)
  return hook
}
