package debugger

import (
    "github.com/davecgh/go-spew/spew"
    "github.com/spiral/roadrunner/service"
    "github.com/spiral/roadrunner/service/rpc"
    "fmt"
)

const ID = "debugger"

type Service struct {
    Config *Config
}

func (s *Service) Init(r *rpc.Service, cfg *Config) (ok bool, err error) {
    s.Config = cfg
    spew.Dump(cfg)

    return true, nil
}

type Config struct {
    HistorySize uint
    Address     string
}

func (c *Config) Hydrate(cfg service.Config) error {
    return cfg.Unmarshal(&c)
}

type rpcService struct {}

func (ps *rpcService) SendDebugInfo(input string, output *string) error {
    *output = "OK"
    fmt.Println(input)
    return nil
}
