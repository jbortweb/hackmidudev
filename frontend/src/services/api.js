import axios from 'axios'

const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL || 'http://localhost:8000/api',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
})

export const userService = {
  // Sincronizar datos de Clerk con Laravel
  sync(userData, token) {
    return api.post('/user/sync', userData, {
      headers: { Authorization: `Bearer ${token}` }
    })
  },

  // Obtener perfil con proyectos del usuario
  getProfile(token) {
    return api.get('/user/profile', {
      headers: { Authorization: `Bearer ${token}` }
    })
  },

  // Eliminar perfil de usuario
  deleteProfile(token) {
    return api.delete('/user/destroy', {
      headers: { Authorization: `Bearer ${token}` }
    })
  }
}

export const projectService = {
  // Obtener todos los proyectos (con filtros opcionales)
  getAll(params = {}) {
    return api.get('/projects', { params })
  },

  // Obtener un proyecto por ID
  getById(id) {
    return api.get(`/projects/${id}`)
  },

  // Crear un proyecto (Requiere token de Clerk)
  create(data, token) {
    return api.post('/projects', data, {
      headers: { Authorization: `Bearer ${token}` }
    })
  },

  // Actualizar un proyecto (Requiere token de Clerk)
  update(id, data, token) {
    return api.put(`/projects/${id}`, data, {
      headers: { Authorization: `Bearer ${token}` }
    })
  },

  // Eliminar un proyecto (Requiere token de Clerk)
  delete(id, token) {
    return api.delete(`/projects/${id}`, {
      headers: { Authorization: `Bearer ${token}` }
    })
  },

  // Dar/quitar like (Requiere token)
  toggleLike(id, token) {
    return api.post(`/projects/${id}/like`, {}, {
      headers: { Authorization: `Bearer ${token}` }
    })
  },

  // Añadir comentario (Requiere token)
  addComment(projectId, data, token) {
    return api.post(`/projects/${projectId}/comments`, data, {
      headers: { Authorization: `Bearer ${token}` }
    })
  }
}

export default api
