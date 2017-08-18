<?php
$app->get('/events', function ($request, $response, $args) {
    try {
        $cri = $this->criteriaParser->parse($request->getQueryParams());
    } catch (Exception $e) {
        return $response->withJson([
            'error' => $e->getMessage()
        ]);
    }

    return $response->withJson($this->eventRepo->findByCri($cri));
});

$app->post('/events', function ($request, $response, $args) {
    $data = $request->getParsedBody();

    try {
        $event = $this->eventFactory->createFromArray($data);
        $id = $this->eventRepo->create($event);
    } catch (Exception $e) {
        return $response->withJson([
            'error' => $e->getMessage()
        ]);
    }

    return $response->withJson([
        'id' => $id
    ], 201);
});

$app->get('/events/{id:[0-9]+}', function ($request, $response, $args) {
    $event = $this->eventRepo->findById($args['id']);

    if ($event === null) {
        return $response->withJson([
            'error' => sprintf('Event does not exist: %d', $args['id'])
        ]);
    }

    return $response->withJson($event);
});

$app->post('/events/{id:[0-9]+}', function ($request, $response, $args) {
    $data = $request->getParsedBody();
    $fields = array_keys($data);
    $data['id'] = $args['id'];

    try {
        $event = $this->eventFactory->createFromArray($data);
        $this->eventRepo->update($event, $fields);
    } catch (Exception $e) {
        return $response->withJson([
            'error' => $e->getMessage()
        ]);
    }

    return $response->withStatus(204);
});
