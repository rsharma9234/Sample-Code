import common from 'feathers-hooks-common'

const schema = {
  include: [
    {
      service: 'time-templates',
      nameAs: 'template',
      parentField: 'timeTemplateId',
      childField: 'id'
    }
  ]
}

export default common.populate({ schema, profile: true })
