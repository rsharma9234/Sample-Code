import moment from 'moment'
import React, { Component, PropTypes } from 'react'
import IconButton from 'material-ui/IconButton'
import ArrowLeft from 'material-ui/svg-icons/hardware/keyboard-arrow-left'
import ArrowRight from 'material-ui/svg-icons/hardware/keyboard-arrow-right'

import MonthView from './MonthView'

class Calendar extends Component {
  static propTypes = {
    date: PropTypes.instanceOf(moment).isRequired,
    onChange: PropTypes.func,
    onDayClick: PropTypes.func
  }

  static defaultProps = {
    date: moment()
  }

  handleLeftArrowClick = () => {
    const { onChange, date } = this.props
    const newDate = date.clone().subtract(1, 'months')
    onChange && onChange(newDate)
  }

  handleRightArrowClick = () => {
    const { onChange, date } = this.props
    const newDate = date.clone().add(1, 'months')
    onChange && onChange(newDate)
  }

  handleDayClick = (selectedDate) => {
    const { onDayClick } = this.props
    onDayClick && onDayClick(selectedDate)
  }

  render () {
    const { content, date , userId } = this.props

    return (
      <div className='calendar'>
        <header className='calendar-header'>
          <h4 className='calendar-header-title'>{date.format('MMMM YYYY')}</h4>
          <div className='calendar-header-actions'>
            <IconButton tooltip='vorheriger Monat' onClick={this.handleLeftArrowClick}>
              <ArrowLeft />
            </IconButton>
            <IconButton tooltip='nächster Monat' onClick={this.handleRightArrowClick}>
              <ArrowRight />
            </IconButton>
          </div>
        </header>
        <MonthView content={content} userId={parseInt(userId, 10)} date={date} onClick={this.handleDayClick} />
      </div>
    )
  }
}

export default Calendar
