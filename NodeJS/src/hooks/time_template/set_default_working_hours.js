import _ from 'lodash'
import { checkContext, getItems, replaceItems } from 'feathers-hooks-common/lib/utils'

const defaultWorkingHours = []
// monday = 0 through sunday = 6
for (let i = 0; i < 7; i++) {
  if (i < 4) {
    defaultWorkingHours.push({ timeRanges: [
      { from: { hour: 6, minute: 0 }, to: { hour: 8, minute: 0 } },
      { from: { hour: 8, minute: 30 }, to: { hour: 11, minute: 0 } },
      { from: { hour: 12, minute: 0 }, to: { hour: 16, minute: 0 } }
    ]})
  } else if (i === 4) {
    defaultWorkingHours.push({ timeRanges: [
      { from: { hour: 6, minute: 0 }, to: { hour: 8, minute: 0 } },
      { from: { hour: 8, minute: 30 }, to: { hour: 11, minute: 0 } },
      { from: { hour: 12, minute: 0 }, to: { hour: 15, minute: 0 } }
    ]})
  } else {
    defaultWorkingHours.push({ timeRanges: [] })
  }
}

/**
 * Sets the workingHours field to a default value if none is provided
 * @param {object} hook - feathers hook object
*/
export default function (hook) {
  checkContext(hook, 'before', 'create')

  let timeTemplates = getItems(hook)

  // support creation of multiple items (array)
  if (Array.isArray(timeTemplates)) {
    timeTemplates = timeTemplates.map((timeTemplate) => {
      if (_.isNil(timeTemplate.workingHours)) {
        return { ...timeTemplate, workingHours: defaultWorkingHours }
      }

      return timeTemplate
    })
  } else {
    if (_.isNil(timeTemplates.workingHours)) {
      timeTemplates.workingHours = defaultWorkingHours
    }
  }
  replaceItems(hook, timeTemplates)

  return hook
}
