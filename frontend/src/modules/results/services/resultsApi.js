import apiClient from '../../../services/api'

export const resultsApi = {
  // Public Endpoints
  async inquireStudentResults({ student_id_number, academic_term_id }) {
    const response = await apiClient.post('/student-portal/results', {
      student_id_number,
      academic_term_id,
    })
    return response.data
  },

  async simulateStudentRegistration({ student_id_number, selected_courses }) {
    const response = await apiClient.post('/student-portal/simulate-registration', {
      student_id_number,
      selected_courses,
    })
    return response.data?.data || response.data
  },
}

export default resultsApi

