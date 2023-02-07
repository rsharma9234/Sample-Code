import moment from 'moment-timezone'

export default class Service {
  setup (app) {
    this.app = app
  }

  find ({ query }) {
    const { userId } = query
    const userTimeTemplates = this.app.service('user-time-templates')
    const userState = this.app.service('user-state')
    let result = {}

    return userTimeTemplates.find({
      paginate: false,
      query: {
        userId,
        $sort: { validFrom: 1 }
      }
    }).then((userTimeTemplates) => {
      if (userTimeTemplates.length === 0) {
        return
      }

      const from = moment(userTimeTemplates[0].validFrom).toISOString()
      const to = moment().endOf('day').toISOString()
      result.maxVacationTime = userTimeTemplates[0].template.vacationTime / 100

      return userState.find({
        query: { userId, from, to }
      })
    }).then((userStates) => {
      const total = userStates.reduce((prev, userState) => {
        const {
          actual,
          timeBalance,
          compensationTimeBalance,
          tooLate,
          vacationTime,
          illnessTime,
          accidentTime,
          daysIll,
          daysUnfall,
          compensationTime
        } = prev

        const isCurrentYear = moment(userState.date).isSame(moment(), 'year')

        return {
          actual: actual + userState.targetDuration,
          timeBalance: timeBalance + userState.balance,
          compensationTimeBalance: compensationTimeBalance + userState.compensationTimeBalance,
          tooLate: isCurrentYear && userState.wasLate ? tooLate + 1 : tooLate,
          vacationTime: vacationTime + userState.vacation,
          illnessTime: illnessTime + userState.illness,
          accidentTime: accidentTime + userState.accident,
          daysIll: daysIll + (userState.illness > 0 ? 1 : 0),
          daysUnfall: daysUnfall + (userState.accident > 0 ? 1 : 0),
          fixedTime: userState.fixedTime,
          compensationTime: compensationTime + userState.compensationTime,
          dailyWorkingHoursWithoutCompensationTime: userState.dailyWorkingHoursWithoutCompensationTime
        }
      }, {
        actual: 0,
        timeBalance: 0,
        compensationTimeBalance: 0,
        tooLate: 0,
        vacationTime: 0,
        illnessTime: 0,
        accidentTime: 0,
        daysIll: 0,
        daysUnfall: 0,
        fixedTime: '',
        compensationTime: 0,
        dailyWorkingHoursWithoutCompensationTime: 0
      })

      return {
        ...result,
        ...total
      }
    })
  }

  calculateTargetHours (timeTemplates) {
    return timeTemplates.reduce(({ regularTime, compensationTime }, timeTemplate) => {
      const currentDate = moment.tz(timeTemplate.validFrom, 'Europe/Zurich').locale('de')

      while (currentDate.isSameOrBefore(moment())) {
        const { timeRanges, targetDuration } = timeTemplate.template.workingHours[currentDate.weekday()]

        regularTime += targetDuration

        if (timeRanges.length > 0) {
          compensationTime += timeTemplate.template.compensationTime
        }

        currentDate.add(1, 'day')
      }

      return {
        regularTime,
        compensationTime
      }
    }, { regularTime: 0, compensationTime: 0 })
  }
}
