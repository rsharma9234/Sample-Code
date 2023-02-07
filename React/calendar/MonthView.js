import _find from 'lodash/find'
import moment from 'moment'
import React, { Component, PropTypes } from 'react'
import { graphql } from 'react-apollo'
import gql from 'graphql-tag'
import flow from 'lodash/flow'
import { withLoader } from '../util'


class MonthView extends Component {
  static propTypes = {
    date: PropTypes.instanceOf(moment).isRequired,
    onClick: PropTypes.func,
    userId: PropTypes.number.isRequired
  }

  handleClick = (selectedDate) => {
    const { onClick } = this.props

    return function (event) {
      event.stopPropagation()
      onClick && onClick(selectedDate)
    }
  }
  
  getColor = (currentDate) => {
    const { data: { usrTimetemplate } } = this.props
    let holiday_arr = []
    if (usrTimetemplate !== undefined) {
      
      for(var i=0 ; i < usrTimetemplate[0].holidays.length ; i++){
        holiday_arr[i] = usrTimetemplate[0].holidays[i].date
       }
       
        if ( this.in_array(currentDate , holiday_arr) === 1 ) {
          return 'light-blue'
        }
        else {
          for(var j=0 ; j < usrTimetemplate[0].vacations.length ; j++) {
            if( usrTimetemplate[0].vacations[j].type === 0 || parseInt(usrTimetemplate[0].vacations[j].type , 10) === parseInt(usrTimetemplate[0].userId , 10) ) {
              let vacation_from = moment(usrTimetemplate[0].vacations[j].vacation_from)
              if(vacation_from.isSameOrAfter(moment(usrTimetemplate[0].validFrom),'day')) {
                if(currentDate.isSameOrAfter(moment(usrTimetemplate[0].vacations[j].vacation_from),'day')) {
                  if ( currentDate.isSameOrBefore(moment(usrTimetemplate[0].vacations[j].vacation_to), 'day') ) {
                    return 'light-blue-900'
                  }
                }
              }
              else{
                if(currentDate.isSameOrAfter(moment(usrTimetemplate[0].validFrom),'day')) {
                  if ( currentDate.isSameOrBefore(moment(usrTimetemplate[0].vacations[j].vacation_to), 'day') ) {
                    return 'light-blue-900'
                  }
                }
              }
            }
          }
        }
      }
    return 'no-light-blue'
  }
  
  in_array = (currentDate, holiday_arr) => {
    for (var i=0, len=holiday_arr.length;i<len;i++) {
        if (currentDate.isSame(moment(holiday_arr[i]), 'day')) {
           return 1
        }
    }
    return -1
  }
  
  renderWeeks = (weeks) => weeks.map(({ weeknumber, ...rest }) => (
    <tr key={weeknumber}>
      {this.renderDays({...rest})}
    </tr>
  ))

  renderDays ({ weekStart, weekEnd }) {
    const { content, date } = this.props
    const currentDate = moment(weekStart).clone()
    const days = []
  
    while (currentDate.isSameOrBefore(weekEnd)) {
      
      const dateIsInDisplayedMonth = currentDate.month() === date.month()
      
      let weeKendDay = moment(currentDate).format('dddd')
      
      const flag = weeKendDay === 'Samstag' || weeKendDay === 'Sonntag' ? false : true
      
      days.push(
          <td
            className={dateIsInDisplayedMonth && flag ? this.getColor(currentDate) : ''}
            key={currentDate.dayOfYear()}
            onClick={dateIsInDisplayedMonth && this.handleClick(currentDate.clone())}
          >
          
          {
            dateIsInDisplayedMonth
            ? <div className={`${dateIsInDisplayedMonth ? '' : 'previous '}date`}>
                {currentDate.date()}
              </div>
            : null
          }
          {
            dateIsInDisplayedMonth
            ? _find(content, ({ props: { date } }) => currentDate.isSame(moment(date), 'day'))
            : null
          }
        </td>
      )

      currentDate.add(1, 'day')
    }
    return days
  }

  render () {
    const { date } = this.props
    const currentDate = moment(date).startOf('month').startOf('week')
    const endDate = moment(date).endOf('month')
    
    const weeks = [];
    while (currentDate.isSameOrBefore(endDate)) {
      weeks.push({
        weeknumber: currentDate.week(),
        weekStart: currentDate.clone(),
        weekEnd: currentDate.clone().endOf('week')
      })

      currentDate.add(7, 'days')
    }

    return (
      <table className='calendar-month-view'>
        <thead>
          <tr>
            <th>Mo</th>
            <th>Di</th>
            <th>Mi</th>
            <th>Do</th>
            <th>Fr</th>
            <th>Sa</th>
            <th>So</th>
          </tr>
        </thead>
        <tbody>
          {this.renderWeeks(weeks)}
        </tbody>
      </table>
    )
  }
}

const DAYS_QUERY = gql/* GraphQL */`
  query usrTimetemplate($userId: ID!) {
    usrTimetemplate(userId: $userId) {
      validFrom,
      userId
      holidays {
        date
      }
      vacations{
        vacation_from
        vacation_to
        type
      }
    }
  }
`;

export default flow([
  withLoader(),
  graphql(DAYS_QUERY, {
    options: ({ userId }) => ({
      variables: { userId: userId }
    }),
    refetchQueries: ['usrTimetemplate' ]
  })
])(MonthView)
