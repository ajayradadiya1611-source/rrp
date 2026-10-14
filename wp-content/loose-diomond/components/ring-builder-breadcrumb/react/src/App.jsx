import React from 'react'
import RingBuilderBreadcrumb from './components/RingBuilderBreadcrumb'

export default function App({ currentPage, rbData, urls }) {
  return <RingBuilderBreadcrumb currentPage={currentPage} rbData={rbData} urls={urls} />
}
