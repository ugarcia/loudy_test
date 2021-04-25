package main

import (
	"bufio"
	"fmt"
	"os"
	"strconv"
	"sync"
)

type mutexSum struct {
	sum  int
	lock sync.Mutex
}

func main() {

	args := os.Args[1:]
	wg := sync.WaitGroup{}
	sum := mutexSum{}
	for _, fName := range args {
		wg.Add(1)
		go sumFile(&wg, &sum, fName)
	}
	wg.Wait()
	fmt.Printf("Total sum: %d", sum.sum)
}

func sumFile(wg *sync.WaitGroup, sum *mutexSum, fName string) {

	defer wg.Done()
	f, err := os.Open(fName)
	if err != nil {
		panic(fmt.Sprintf("Cannot open file %s: %s", fName, err))
	}
	defer f.Close()

	var fSum int
	scanner := bufio.NewScanner(f)
	for scanner.Scan() {
		line := scanner.Text()
		lNum, err := strconv.Atoi(line)
		if err != nil {
			panic(fmt.Sprintf("Cannot parse %s as number in file %s", line, fName))
		}
		fSum += lNum
	}
	sum.lock.Lock()
	defer sum.lock.Unlock()
	sum.sum += fSum
}
