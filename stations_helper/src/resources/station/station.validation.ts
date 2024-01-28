import Joi from 'joi';

const show = Joi.object({
    id: Joi.string().required(),
    name: Joi.string().required(),
    location: Joi.object({
        type: Joi.string(),
        coordinates: Joi.array().items(Joi.number()),
    }),
    company_id: Joi.number(),
    created_at: Joi.string(),
    updated_at: Joi.string(),
});

export default { show };
